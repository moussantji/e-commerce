/**
 * Téléversement d'images en JSON + base64.
 *
 * Le pare-feu de production rejette les requêtes multipart avant PHP. Ce
 * script lit donc les fichiers côté navigateur et les envoie une par une à
 * `/uploads/images` en `application/json`.
 *
 * Deux modes :
 *  - data-livewire="1" : après les téléversements, appelle la méthode
 *    Livewire `addUploadedImages(paths)` ;
 *  - sinon : ajoute des champs cachés `uploaded_images[]` et des aperçus au
 *    formulaire classique (espace vendeur).
 */
(function () {
    'use strict';

    var MAX_BYTES = 2 * 1024 * 1024;
    var pendingUploads = 0;

    function statusElement(input) {
        if (!input.dataset.status) return null;
        return document.querySelector(input.dataset.status);
    }

    function setStatus(input, message, isError) {
        var el = statusElement(input);
        if (!el) return;
        el.textContent = message || '';
        el.style.color = isError ? '#be123c' : '';
        el.setAttribute('role', 'status');
    }

    function readFileAsBase64(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onerror = function () {
                reject(new Error('Impossible de lire « ' + file.name + ' ».'));
            };
            reader.onload = function () {
                var result = String(reader.result || '');
                var comma = result.indexOf(',');
                resolve(comma >= 0 ? result.slice(comma + 1) : result);
            };
            reader.readAsDataURL(file);
        });
    }

    async function uploadFile(input, file) {
        if (file.size > MAX_BYTES) {
            throw new Error('« ' + file.name + ' » dépasse 2 Mo.');
        }

        var data = await readFileAsBase64(file);
        var response = await fetch(input.dataset.uploadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': input.dataset.csrf || '',
            },
            body: JSON.stringify({
                name: file.name,
                mime: file.type || 'image/jpeg',
                data: data,
            }),
        });

        var payload = null;
        try {
            payload = await response.json();
        } catch (e) {
            payload = null;
        }

        if (!response.ok) {
            var message =
                (payload && payload.message) ||
                (payload && payload.errors && payload.errors.data && payload.errors.data[0]) ||
                'Le téléversement a échoué (' + response.status + ').';
            throw new Error(message);
        }

        if (!payload || !payload.path) {
            throw new Error('Réponse de téléversement invalide.');
        }

        return payload;
    }

    function addPendingPath(input, path, url) {
        var inputsHost = input.dataset.inputs ? document.querySelector(input.dataset.inputs) : null;
        var previewHost = input.dataset.preview ? document.querySelector(input.dataset.preview) : null;

        if (inputsHost) {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'uploaded_images[]';
            hidden.value = path;
            hidden.setAttribute('data-uploaded-input', path);
            inputsHost.appendChild(hidden);
        }

        if (!previewHost) return;

        var span = document.createElement('span');
        span.className = 'aprev';
        span.setAttribute('data-uploaded-preview', path);

        var img = document.createElement('img');
        img.src = url || ('/storage/' + path);
        img.alt = 'Aperçu';

        var button = document.createElement('button');
        button.type = 'button';
        button.setAttribute('aria-label', 'Retirer');
        button.setAttribute('data-remove-uploaded', path);
        button.textContent = '×';

        span.appendChild(img);
        span.appendChild(button);
        previewHost.appendChild(span);
    }

    function currentCount(input) {
        var count = parseInt(input.dataset.existingCount || '0', 10) || 0;
        if (input.dataset.livewire === '1') return count;

        var inputsHost = input.dataset.inputs ? document.querySelector(input.dataset.inputs) : null;
        if (inputsHost) {
            count += inputsHost.querySelectorAll('input[name="uploaded_images[]"]').length;
        }

        return count;
    }

    async function handleFiles(input) {
        var files = Array.prototype.slice.call(input.files || []);
        if (!files.length) return;

        var max = parseInt(input.dataset.max || '10', 10) || 10;
        var remaining = Math.max(0, max - currentCount(input));

        if (remaining <= 0) {
            input.value = '';
            setStatus(input, 'Limite de ' + max + ' image(s) atteinte.', true);
            alert('Limite de ' + max + ' image(s) atteinte.');
            return;
        }

        if (files.length > remaining) {
            alert(
                'Seules les ' + remaining + ' premières images seront ajoutées (limite de ' +
                max + ').',
            );
            files = files.slice(0, remaining);
        }

        // Réinitialise la valeur pour permettre de choisir à nouveau le même
        // fichier après un retrait.
        input.value = '';

        pendingUploads++;
        setStatus(input, 'Téléversement de ' + files.length + ' image(s)…', false);

        var uploaded = [];
        var lastError = null;

        for (var i = 0; i < files.length; i++) {
            try {
                setStatus(input, 'Téléversement ' + (i + 1) + '/' + files.length + '…', false);
                var result = await uploadFile(input, files[i]);
                uploaded.push(result);
            } catch (error) {
                lastError = error;
                break;
            }
        }

        try {
            if (uploaded.length > 0) {
                if (input.dataset.livewire === '1') {
                    var root = input.closest('[wire\\:id]');
                    var componentId = root && root.getAttribute('wire:id');
                    var component = componentId && window.Livewire
                        ? window.Livewire.find(componentId)
                        : null;

                    if (!component || typeof component.call !== 'function') {
                        throw new Error('Composant Livewire indisponible. Rechargez la page.');
                    }

                    await component.call(
                        'addUploadedImages',
                        uploaded.map(function (item) { return item.path; }),
                    );
                } else {
                    uploaded.forEach(function (item) {
                        addPendingPath(input, item.path, item.url);
                    });
                }
            }

            if (lastError) {
                throw lastError;
            }

            setStatus(
                input,
                uploaded.length + ' image(s) ajoutée(s). Pensez à enregistrer le produit.',
                false,
            );
        } catch (error) {
            setStatus(input, error.message || 'Erreur pendant le téléversement.', true);
            alert(error.message || 'Erreur pendant le téléversement.');
        } finally {
            pendingUploads = Math.max(0, pendingUploads - 1);
        }
    }

    document.addEventListener('change', function (event) {
        var input = event.target.closest('input[data-json-upload]');
        if (input) handleFiles(input);
    });

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-remove-uploaded]');
        if (!button) return;

        event.preventDefault();
        var path = button.getAttribute('data-remove-uploaded');

        document.querySelectorAll('input[data-uploaded-input]').forEach(function (input) {
            if (input.value === path) input.remove();
        });
        document.querySelectorAll('[data-uploaded-preview]').forEach(function (preview) {
            if (preview.getAttribute('data-uploaded-preview') === path) preview.remove();
        });
    });

    // Empêche d'envoyer le formulaire pendant qu'un fichier est en cours
    // d'envoi : le champ caché / la propriété Livewire ne serait pas encore à
    // jour.
    document.addEventListener(
        'submit',
        function (event) {
            if (pendingUploads > 0) {
                event.preventDefault();
                event.stopPropagation();
                alert('Téléversement en cours. Patientez quelques secondes avant d’enregistrer.');
            }
        },
        true,
    );
})();

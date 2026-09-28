<div class="row align-items-center g-4">
    <!-- Commandes en attente -->
    <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center">
            <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-warning-light"
                    data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-warning"
                    data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-clock text-warning"
                    data-fa-transform="shrink-2 up-8 right-6"></span>
            </span>
            <div class="ms-3">
                <h4 class="mb-0">{{ $pendingOrders }} commande(s)</h4>
                <p class="text-body-secondary fs-9 mb-0">En attente</p>
            </div>
        </div>
    </div>

    <!-- Commandes payées -->
    <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center">
            <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-info-light"
                    data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-info"
                    data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-credit-card text-info"
                    data-fa-transform="shrink-2 up-8 right-6"></span>
            </span>
            <div class="ms-3">
                <h4 class="mb-0">{{ $processingOrders }} commande(s)</h4>
                <p class="text-body-secondary fs-9 mb-0">Payées</p>
            </div>
        </div>
    </div>

    <!-- Commandes expédiées -->
    <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center">
            <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-primary-light"
                    data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-primary"
                    data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-truck text-primary"
                    data-fa-transform="shrink-2 up-8 right-6"></span>
            </span>
            <div class="ms-3">
                <h4 class="mb-0">{{ $shippedOrders }} commande(s)</h4>
                <p class="text-body-secondary fs-9 mb-0">Expédiées</p>
            </div>
        </div>
    </div>

    <!-- Commandes livrées -->
    <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center">
            <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-success-light"
                    data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-success"
                    data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-check text-success"
                    data-fa-transform="shrink-2 up-8 right-6"></span>
            </span>
            <div class="ms-3">
                <h4 class="mb-0">{{ $deliveredOrders }} commande(s)</h4>
                <p class="text-body-secondary fs-9 mb-0">Livrées</p>
            </div>
        </div>
    </div>

    <!-- Commandes annulées -->
    <div class="col-12 col-md-auto">
        <div class="d-flex align-items-center">
            <span class="fa-stack" style="min-height: 46px; min-width: 46px;">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-danger-light"
                    data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-danger"
                    data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-times text-danger"
                    data-fa-transform="shrink-2 up-8 right-6"></span>
            </span>
            <div class="ms-3">
                <h4 class="mb-0">{{ $cancelledOrders }} commande(s)</h4>
                <p class="text-body-secondary fs-9 mb-0">Annulées</p>
            </div>
        </div>
    </div>
</div>

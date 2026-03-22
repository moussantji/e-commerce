@extends('base')

@section('title', ' ')

@section('content')

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">

        <!-- ============================================-->
        <!-- <section> begin ============================-->
        @include('section-begin')
        <!-- <section> close ============================-->
        <!-- ============================================-->

        @include('partials.nav')

        <!-- ============================================-->
        <!-- LISTE CATÉGORIES PRINCIPALES -->
        <!-- ============================================-->
        @if (!isset($category))
            <section class="pt-5 pb-9">
                <div class="container-small">
                    <nav class="mb-3" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">Accueil</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Toutes les Categories
                            </li>
                        </ol>
                    </nav>

                    <h2 class="mb-1">All Categories</h2>
                    <p class="mb-5 text-body-tertiary fw-semibold">Essential for a better life</p>

                    <div class="row gx-3 gy-5">
                        @foreach ($categories as $category)
                            @include('client.categories._cards', ['item' => $category])
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ============================================-->
            <!-- CATÉGORIE PRINCIPALE + SOUS-CATÉGORIES ENSEMBLE -->
        @elseif(isset($category) && isset($subcategories))
            <section class="pt-5 pb-9">
                <div class="container-small">
                    <nav class="mb-3" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">Accueil</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('categories.index') }}">Catégories</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $category->name }}
                            </li>
                        </ol>
                    </nav>

                    <h2 class="mb-1">{{ $category->name }}</h2>
                    <p class="mb-5 text-body-tertiary fw-semibold">
                        {{ $category->description ?? 'Essential for a better life' }}</p>

                    <div class="row gx-3 gy-5">

                        {{-- ✅ SOUS-CATÉGORIES --}}
                        @foreach ($subcategories as $subcategory)
                            @include('client.categories._cards', [
                                'item' => $subcategory,
                                'parentSlug' => $category->slug,
                            ])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif



    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

@endsection

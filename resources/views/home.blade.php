<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mindluster</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @vite(['resources/scss/app.scss'])
</head>

<body class="bg-light py-5">
    <div class="container">
        <h5 class="text-center text-primary fw-bold">OUR BEST BOOKS</h5>
        <h3 class="text-center fw-bold mb-5">Discover a most popular Books</h3>

        <div class="books row row-cols-1 row-cols-md-2 row-cols-lg-3">
            @foreach ($books as $book)
                <div class="col mb-4">
                    <div class="book_card card border-0 shadow-sm px-3 pt-3" dir="rtl">
                        <h5 class="book_card_title">{{ $book['title'] }}</h5>

                        <div class="book_card_author fw-bold text-primary">{{ $book['author'] }}</div>

                        @if (!is_null($book['lang']) || !is_null($book['size']) || !is_null($book['pages_count']))
                            <hr class="mb-0">
                            <div
                                class="book_card_footer d-flex align-items-center justify-content-between text-muted py-2">
                                @if (!is_null($book['lang']))
                                    <div class="d-flex flex-column">
                                        <span>اللغه</span>
                                        <div>{{ $book['lang'] }}</div>
                                    </div>
                                @endif
                                @if (!is_null($book['size']))
                                    <div class="d-flex flex-column">
                                        <span>الحجم</span>
                                        <div>{{ $book['size'] }}</div>
                                    </div>
                                @endif
                                @if (!is_null($book['pages_count']) && $book['pages_count'] != '')
                                    <div class="d-flex flex-column align-items-center">
                                        <span>الصفحات</span>
                                        <div>{{ $book['pages_count'] }} </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if (!is_null($book['download_link']))
                            <div class="book_card_area_control align-items-center justify-content-center">
                                <a href="{{ $book['download_link'] }}" class="btn btn-primary text-white rounded-circle"
                                    download>
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div id="loading" class="d-none align-items-center justify-content-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header text-danger fw-bold p-3 d-flex align-items-center justify-content-between">
                    <div id="globalMsg"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])

    @if (isset($error) && $error)
        <script>
            window.addEventListener("load", function() {
                fireGlobalMsg("{{ $error }}")
            })
        </script>
    @endif
</body>

</html>

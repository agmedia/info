<!-- Page Title -->
        <section class="page-title dark" data-bs-theme="dark">
            <div class="container">
                <div class="page-title-row">
                    <div class="page-title-content">
                        <h1>{{ $item->title }}</h1>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <!--                        <li class="breadcrumb-item"><a href="#">Templates</a></li>
                                                    <li class="breadcrumb-item active" aria-current="page">Page Titles</li>-->
                        </ol>
                    </nav>

                </div>
            </div>
        </section>

        <!-- Content -->
        <section id="content">
            <div class="content-wrap">
                <div class="container">
                    <div class="row gx-5 col-mb-80">
                        <main class="postcontent col-lg-9">
                            <div class="single-post mb-0">
                                <div class="entry">
                                    <div class="entry-title">
                                        <p class="lead">{{ $item->short_description }}</p>
                                    </div>
                                    <div class="entry-meta">
                                        <ul>
                                            <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($item->created_at)->format('d.m.Y') }}</li>
                                            <li><a href="#"><i class="uil uil-user"></i> admin</a></li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <p>{!! $item->description !!}</p>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </section>
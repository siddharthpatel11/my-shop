@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Global Meta Tags</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted font-14">
                        Manage SEO and Open Graph Meta Tags for static pages (like Home, Contact Us) and dynamic CMS Pages
                        here.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Page Name</th>
                                    <th>Type</th>
                                    <th>SEO Configured</th>
                                    <th style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pages as $p)
                                    <tr>
                                        <td>
                                            <h5 class="m-0 font-weight-normal">{{ $p->name }}</h5>
                                            <span class="text-muted"><small>Identifier: {{ $p->identifier }}</small></span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $p->type == 'Static Page' ? 'bg-primary' : 'bg-info' }}">
                                                {{ $p->type }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($p->meta && ($p->meta->seo_title || $p->meta->seo_description))
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>
                                                    Yes</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>
                                                    No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.meta-tags.edit', $p->identifier) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i> Edit Tags
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel CRUD - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <h1 class="display-4 mb-4">Laravel CRUD Application</h1>
                <p class="lead mb-4">Manage your products, categories, sizes, and colors</p>

                @auth
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fa fa-dashboard"></i> Go to Dashboard
                    </a>
                @else
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fa fa-sign-in"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-success btn-lg">
                            <i class="fa fa-user-plus"></i> Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>

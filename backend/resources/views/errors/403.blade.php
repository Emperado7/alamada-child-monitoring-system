<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Forbidden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f0f2f5">
    <div class="text-center">
        <div style="font-size:6rem;font-weight:800;color:#C62828;line-height:1">403</div>
        <h4 class="mt-2 mb-1">Access Denied</h4>
        <p class="text-muted mb-4">{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}</p>
        <a href="javascript:history.back()" class="btn btn-danger">Go Back</a>
    </div>
</body>
</html>

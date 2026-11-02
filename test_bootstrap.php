<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Test - FindNaukri.in</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-section {
            padding: 2rem;
            margin: 2rem 0;
            border: 2px solid #007bff;
            border-radius: 8px;
        }
        .status-success {
            color: green;
            font-weight: bold;
        }
        .status-error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5 mb-4">Bootstrap & CSS Test</h1>

        <div class="test-section">
            <h2>Container Test</h2>
            <p>If you see this centered with proper padding, Bootstrap is working.</p>
        </div>

        <div class="row">
            <div class="col-md-6 test-section" style="background: #f0f0f0;">
                <h3>Column 1 (col-md-6)</h3>
                <p>This should take 50% width on medium screens and up.</p>
            </div>
            <div class="col-md-6 test-section" style="background: #e0e0e0;">
                <h3>Column 2 (col-md-6)</h3>
                <p>This should take 50% width on medium screens and up.</p>
            </div>
        </div>

        <div class="test-section">
            <h2>Button Test</h2>
            <button class="btn btn-primary me-2">Primary Button</button>
            <button class="btn btn-secondary me-2">Secondary Button</button>
            <button class="btn btn-success">Success Button</button>
        </div>

        <div class="test-section">
            <h2>Form Test</h2>
            <div class="mb-3">
                <label for="testInput" class="form-label">Test Input</label>
                <input type="text" class="form-control" id="testInput" placeholder="This should have Bootstrap styling">
            </div>
            <div class="mb-3">
                <label for="testSelect" class="form-label">Test Select</label>
                <select class="form-select" id="testSelect">
                    <option>Option 1</option>
                    <option>Option 2</option>
                </select>
            </div>
        </div>

        <div class="test-section">
            <h2>Card Test</h2>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Card Title</h5>
                    <p class="card-text">This is a Bootstrap card component.</p>
                    <button class="btn btn-primary">Card Button</button>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>Diagnostic Information</h2>
            <p><strong>Screen Width:</strong> <span id="screenWidth"></span></p>
            <p><strong>Bootstrap Loaded:</strong> <span id="bootstrapStatus" class="status-success">Checking...</span></p>
            <p><strong>Custom CSS Path:</strong> /assets/css/style.css</p>
            <p><strong>Custom CSS Exists:</strong> <span id="cssStatus" class="status-success">Checking...</span></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check screen width
        document.getElementById('screenWidth').textContent = window.innerWidth + 'px';

        // Check if Bootstrap loaded
        if (typeof bootstrap !== 'undefined') {
            document.getElementById('bootstrapStatus').textContent = '✓ Bootstrap JS Loaded';
            document.getElementById('bootstrapStatus').className = 'status-success';
        } else {
            document.getElementById('bootstrapStatus').textContent = '✗ Bootstrap JS Not Loaded';
            document.getElementById('bootstrapStatus').className = 'status-error';
        }

        // Check if custom CSS exists
        fetch('/assets/css/style.css')
            .then(response => {
                if (response.ok) {
                    document.getElementById('cssStatus').textContent = '✓ Custom CSS Found';
                    document.getElementById('cssStatus').className = 'status-success';
                } else {
                    document.getElementById('cssStatus').textContent = '✗ Custom CSS Not Found';
                    document.getElementById('cssStatus').className = 'status-error';
                }
            })
            .catch(error => {
                document.getElementById('cssStatus').textContent = '✗ Error Checking CSS';
                document.getElementById('cssStatus').className = 'status-error';
            });
    </script>
</body>
</html>

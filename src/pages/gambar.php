<?php
require_once "../config/helper.php";
// Store the base path in a variable to use later
$basePath = Helper::basePath();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="file"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <h2>Upload Image</h2>
    <form id="imageUploadForm" enctype="multipart/form-data" action="<?php echo htmlspecialchars($basePath . 'gambar'); ?>" method="POST">
        <div class="form-group">
            <label for="image">Select Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit">Upload Image</button>
    </form>

    <script>
        document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const endpoint = "<?php echo Helper::basePath() . 'gambar'; ?>";

            fetch(endpoint, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show "wow success" popup
                        alert(data.message || 'wow success');
                        document.getElementById('imageUploadForm').reset();
                    } else {
                        alert('Failed! ' + (data.message || 'Image upload failed.'));
                    }
                })
                .catch(error => {
                    alert('Failed! An error occurred during upload.');
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>
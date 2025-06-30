<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Bài Viết Mới</title>
    <link rel="stylesheet" href="../style.css"> 
    <style>
        .admin-container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-group textarea {
            min-height: 250px;
            resize: vertical;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Tạo Bài Viết Mới</h2>
        <form action="save_post.php" method="POST">
            <div class="form-group">
                <label for="title">Tiêu đề:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Tên tác giả:</label>
                <input type="text" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="content">Nội dung:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Lưu Bài Viết</button>
        </form>
    </div>
</body>
</html>
$stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        throw new Exception('Product not found');
    }

    $imageName = $existing['image'];

    // Image upload (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $uploadDir = "../../../TheSpiceNepal/img/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Delete old image (if not default)
        if ($imageName && file_exists($uploadDir . $imageName)) {
            unlink($uploadDir . $imageName);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('prod_', true) . '.' . $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $uploadDir . $imageName
        );
    }

    // Update query
    $sql = "UPDATE products SET
                name = :name,
                price = :price,
                old_price = :old_price,
                category = :category,
                on_sale = :on_sale,
                image = :image
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name'      => $name,
        ':price'     => $price,
        ':old_price' => $oldPrice,
        ':category'  => $category,
        ':on_sale'   => $onSale,
        ':image'     => $imageName,
        ':id'        => $id
    ]);
=======
    // Get existing image
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        throw new Exception('Product not found');
    }

    $imageName = $existing['image_path'];

    // Image upload (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Delete old image (if not default)
        if ($imageName && file_exists($uploadDir . $imageName)) {
            unlink($uploadDir . $imageName);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . "_" . basename($_FILES['image']['name']);

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $uploadDir . $imageName
        );
    }

    // Update query
    $sql = "UPDATE products SET
                name = :name,
                price = :price,
                old_price = :old_price,
                category = :category,
                on_sale = :on_sale,
                in_stock = :in_stock,
                image_path = :image_path
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name'      => $name,
        ':price'     => $price,
        ':old_price' => $oldPrice,
        ':category'  => $category,
        ':on_sale'   => $onSale,
        ':in_stock'  => isset($_POST['inStock']) ? 1 : 0,
        ':image_path' => $imageName,
        ':id'        => $id
    ]);

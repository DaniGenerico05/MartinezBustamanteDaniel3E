<?php
    include "includes/header.php";
    require "includes/config/conn.php";
   
    $db = connect(); 

    $query_sellers = "SELECT id, name FROM seller;";
    $sellers = mysqli_query($db, $query_sellers);

    var_dump($_POST);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = isset($_POST["title"]) ? $_POST["title"] : '';
        $price = isset($_POST["price"]) ? $_POST["price"] : 0;
        $image = isset($_POST["image"]) ? $_POST["image"] : '';
        $description = isset($_POST["description"]) ? $_POST["description"] : '';
        $rooms = isset($_POST["rooms"]) ? $_POST["rooms"] : 0;
        $wc = isset($_POST["wc"]) ? $_POST["wc"] : 0;
        $garage = isset($_POST["garage"]) ? $_POST["garage"] : 0;
        $timeslamp = isset($_POST["timeslamp"]) ? $_POST["timeslamp"] : '';
        $seller = isset($_POST["id_seller"]) ? $_POST["id_seller"] : null;

        if($title && $price && $description && $rooms && $wc && $garage && $timeslamp && $seller) {
            $sql = "INSERT INTO property (title, price, image, description, rooms, wc, garage, timeslamp, id_seller) VALUES ('$title', $price, '$image', '$description', $rooms, $wc, $garage, '$timeslamp', $seller)";
            if (mysqli_query($db, $sql)) {
                echo "Property created successfully!";
            } else {
                echo "Error: " . mysqli_error($db);
            }
        } else {
            echo "Por favor, completa todos los campos.";
        }
    }
?>

<section>
    <h2>Properties Form</h2>
    <div>
        <form action="propierties.php" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Fill all Form Fields</legend>
                <div>
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" required>
                </div>
                <div>
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" required>
                </div>
                <div>
                    <label for="image">Image</label>
                    <input type="file" accept="image/*" name="image" id="image">
                </div>
                <div>
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" required>
                </div>
                <div>
                    <label for="rooms">Rooms</label>
                    <input type="number" name="rooms" id="rooms" required>
                </div>
                <div>
                    <label for="wc">WC</label>
                    <input type="number" name="wc" id="wc" required>
                </div>
                <div>
                    <label for="garage">Garage</label>
                    <input type="number" name="garage" id="garage" required>
                </div>
                <div>
                    <label for="timeslamp">TimeStamp</label>
                    <input type="date" name="timeslamp" id="timeslamp" required>
                </div>
                <div>
                    <label for="id_seller">Seller</label>
                    <select name="id_seller" id="id_seller" required>
                        <?php while($seller = mysqli_fetch_assoc($sellers)): ?>
                            <option value="<?php echo $seller['id']; ?>"><?php echo $seller['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <button type="submit">Create a New Property</button>
                </div>
            </fieldset>
        </form>
    </div>
</section>
<?php 
    include "includes/footers.php";
?>
<?php include "../../includes/header.php" ;

      require "../../includes/config/conn.php";
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
    connect();
    $db = connect();
    var_dump($_POST);
    $id = isset($_POST["id"]) ? $_POST["id"] : '';
    $name = isset($_POST["name"]) ? $_POST["name"] : '';
    $email = isset($_POST["email"]) ? $_POST['email'] : '';
    $phone = isset($_POST["phone"]) ? $_POST['phone'] : '';
    if ($id && $name && $email && $phone){
    $query = "insert into seller (name, email, phone) values ('$name','$email','$phone')";
    $response = mysqli_query($db, $query);
    if ($response){
        echo "seller creado";
    }else{
        echo "error al crear el seller";
    }
}else{
    echo "favor de llenar los datos";  
    } 
}
?>
<section>
    <h2>Sellers form</h2>
    <div>
        <form action="createSeller.php" method="POST">
            <fielfset>
                <legend> Fill all form fields </legend>
                <div>
                    <label for="id">ID Seller</label>
                    <input type = "number" id="id" name="id">
                </div>
                <div>
                    <label for="name">Seller name</label>
                    <input type = "text" id="name" name="name">
                </div>
                <div>
                    <label for="email">Seller Email</label>
                    <input type = "email" id="email" name="email">
                </div>
                <div>
                    <label for="name">Seller phone</label>
                    <input type = "tel" id="phone" name="phone" max-length = "10">
                </div>
                <div>
                    <button type = "submit">Create a new seller </button>
                </div>
            </fieldset>
        </form>
    </div>

</section>


<?php  include "includes/footers.php" ?>

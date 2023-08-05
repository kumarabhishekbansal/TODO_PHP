<?php
$server = 'localhost';
$username = 'root';
$password = '';
$database = 'todo_project';
$conn = mysqli_connect($server, $username, $password, $database);
if ($conn->connect_errno) {
    die('connection to MYSQL failed : ' . $conn->connect_error);
}
// creating todo item

if (isset($_POST['add'])) {
    $item = $_POST['item'];
    if (!empty($item)) {
        $query = "INSERT INTO todo (name) VALUES ('$item')";
        if (mysqli_query($conn, $query)) {
            echo '
                <center>
                <div class="alert alert-success" role="alert">
                Item added successfully!!
                    </div>
                </center>
            ';
        } else {
            echo mysqli_errno($conn);
        }
    }
}

// update todo item

if (isset($_POST['updateitem'])) {
    $item = $_POST['item'];
    $itemid=$_POST['itemId'];
    // var_dump($item.$itemid);
    if (!empty($item) && !empty($itemid)) {
        // var_dump($item.$itemid);
        $query = "UPDATE todo SET name='$item' WHERE id='$itemid' ";
        if (mysqli_query($conn, $query)) {
            echo '
                <center>
                <div class="alert alert-warning" role="alert">
                Item updated successfully!!
                </div>
                </center>
            ';
        } else {
            echo mysqli_errno($conn);
        }
    }
}

// if action parameter is present

if (isset($_GET['action'])) {
    $itemid = $_GET['item'];
    if ($_GET['action'] == 'done') {
        $query="SELECT status FROM todo WHERE id='$itemid' ";
        $itemselect=mysqli_query($conn,$query);
        $itemname=$itemselect->fetch_assoc();
        if($itemname['status']==1)
        {
            $querysuccess = "UPDATE todo SET status=0 WHERE id='$itemid' ";
            if (mysqli_query($conn, $querysuccess)) {
                echo '
                <center>             
                <div class="alert alert-secondary" role="alert">
                    Marked as Undone successfully!!
                </div>
                </center>
                ';
            } else {
                echo mysqli_errno($conn);
            }
        }else{
            $querysuccess = "UPDATE todo SET status=1 WHERE id='$itemid' ";
            if (mysqli_query($conn, $querysuccess)) {
                echo '
                <center>             
                <div class="alert alert-info" role="alert">
                    Marked as done successfully!!
                </div>
                </center>
                ';
            } else {
                echo mysqli_errno($conn);
            }
        }
        
        
    } elseif ($_GET['action'] == 'delete') {
        $query = "DELETE FROM todo WHERE id='$itemid' ";
        if (mysqli_query($conn, $query)) {
            echo '
            <center>             
            <div class="alert alert-danger" role="alert">
            Item deleted successfully!!
            </div>
            </center>
            ';
        } else {
            echo mysqli_errno($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .done {
            text-decoration: line-through;
        }
    </style>
</head>

<body>
    <main>
        <div class="container pt-5">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <p>Todo List</p>
                        </div>
                        <div class="card-body">
                            <form method="post" action="?=$_SERVER['PHP_SELF']?">
                                <div class="mb-3">
                                    <?php
                                    if (isset($_GET['action'])) {
                                        $itemid = $_GET['item'];
                                        if ($_GET['action'] == 'edit') {
                                            $query="SELECT name FROM todo WHERE id='$itemid' ";
                                            $itemselect=mysqli_query($conn,$query);
                                            $itemname=$itemselect->fetch_assoc();
                                            
                                            echo '
                                            <input type="text" name="item" id="Update a item" class="form-control" value="' . $itemname['name'] . '">
                                            <input type="number" name="itemId" id="Item Id" class="form-control" value="' . $itemid . '" hidden="true">
                                            <input type="submit" value="Update Item" class="btn btn-warning mt-4" name="updateitem" >
                                            
                                            
                                            ';
                                        } else {
                                            echo '
                                            <input type="text" name="item" id="Add a item" class="form-control">
                                            <input type="submit" value="Add Item" class="btn btn-dark mt-4" name="add">
                                            ';
                                        }
                                    } else {
                                        echo '
                                        <input type="text" name="item" id="Add a item" class="form-control">
                                        <input type="submit" value="Add Item" class="btn btn-dark mt-4" name="add">
                                        ';
                                    }
                                    ?>
                                </div>

                            </form>

                            <div class="mt-5 mb-5">



                                <?php
                                $query = "SELECT * FROM todo";
                                $result = mysqli_query($conn, $query);
                                if ($result->num_rows > 0) {
                                    $i = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        $done = $row['status'] == 1 ? 'done' : "";
                                        echo '
                                        <div class="row mt-5">
                                       <div class="col-sm-12 col-md-1">
                                           <h5>' . $i . '</h5>
                                       </div>
                                       <div class="col-sm-12 col-md-5">
                                           <h5 class="' . $done . '">' . $row['name'] . '</h5>
                                       </div>
                                       <div class="col-sm-12 col-md-6">
                                           <a href="?action=done&item=' . $row['id'] . '" class="btn btn-outline-dark">Mark as done</a>
                                           <a href="?action=delete&item=' . $row['id'] . '" class="btn btn-outline-danger">Delete</a>
                                           <a href="?action=edit&item=' . $row['id'] . '" class="btn btn-outline-warning">Edit</a>
                                       </div>
                                   </div>
                                       ';
                                        $i++;
                                    }
                                } else {
                                    echo '
                                        <center>
                                    <img src="folder.jpg" width="50px" alt="empty list"><br /><span>Your List is Empty!!</span>
                                        </center>
                                        ';
                                }
                                ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $(".alert").fadeTo(5000, 500).slideUp(500, function() {
                $(".alert").slideUp(500);
            })
        });
    </script>
</body>

</html>
<?php 
// connect to db.sqlite
$pdo = new PDO('sqlite:db.sqlite');
// check if $_POST['name'] and $_POST['bid'] are set
if (isset($_POST['name']) && isset($_POST['bid'])) {
   // check to see if the bid is numeric and greater than the current highest bid
    if (is_numeric($_POST['bid'])) {
         // get the highest bid
         $stmt = $pdo->prepare('SELECT MAX(bid) as max_bid FROM bids');
         $stmt->execute();
         $max_bid = $stmt->fetch()['max_bid'];
         // if the bid is greater than the highest bid, insert the bid
         if ($_POST['bid'] > $max_bid) {
              $msg = array();
              $msg['type'] = 'success';
              $stmt = $pdo->prepare('INSERT INTO bids (name, bid) VALUES (:name, :bid)');
              $stmt->execute(['name' => $_POST['name'], 'bid' => $_POST['bid']]);
              $msg['msg'] = "Inserted bid for ". $_POST['name'] . " of $" . $_POST['bid'] . "\n";
         } else {
            $msg = array();
            $msg['type'] = 'danger';
            $msg['msg'] = "Bid must be greater than the current highest bid of $max_bid\n";
         }
    } else {
        $msg = array();
        $msg['type'] = 'danger';
        $msg['msg'] = "Bid must be a number\n";
    }
}

?>


<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bid Testr</title>
    <!-- bootstrap 5 cdn-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h1>Bid Test</h1>
        <!-- ui for name and bid, action to test-bid-speed.php -->
        <form
        hx-post="index.php"
        hx-target="#response_msg"
        hx-swap="outerHTML"
        hx-trigger="submit"
        hx-select="#response_msg"
         action="" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="mb-3">
                <label for="bid" class="form-label">Bid</label>
                <input type="number" class="form-control" id="bid" name="bid">
            </div>
            <div id="response_msg" class="alert alert-<?php echo $msg['type'] ?? 'info'; ?>" role="alert">
                <?php echo $msg['msg'] ?? 'Enter a name and bid'; ?>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <div class="row">
            <!-- display the bids with the highest bid first -->
            <div class="col">
                <h2>Bids</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Bid</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody
                    hx-get="index.php"
                    hx-target="#all_bids"
                    hx-swap="outerHTML"
                    hx-select="#all_bids"
                     id="all_bids" hx-trigger="every 1s">
                        <?php
                        // get all the bids ordered by bid descending
                        $stmt = $pdo->prepare('SELECT * FROM bids ORDER BY bid DESC');
                        $stmt->execute();
                        $bids = $stmt->fetchAll();
                        // loop through the bids and display them
                        foreach ($bids as $bid) {
                            echo "<tr><td>{$bid['name']}</td><td>{$bid['bid']}</td><td>{$bid['created_at']}</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
</body>
</html>
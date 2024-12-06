
<?php 
require_once 'includes/connect_data.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $_SESSION['search'] = $_GET['search'];
}

// <!-- Hang so  -->
$tbl_name = 'chuyen_khoan';
$_MINPAGE = 1;
$_DEFAUTLIMIT = 100;
$_OPTIONS = [5, 50, 100, 250, 500];

define('_WEB_HOST','http://'.$_SERVER['HTTP_HOST']); //http://onlab.site

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="LTNC, sao ke, chuyen khoan">
    <meta name="description" content="Lap Trinh Nang Cao">
    <meta name="author" content="LTNC Team">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vietnam Fatherland Front's statements</title>
    <link rel="stylesheet" href="<?php echo _WEB_HOST;?>/templates/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo _WEB_HOST;?>/templates/css/style.css">
</head>
<body>
    <h1 class="Title">Search for the Vietnam Fatherland Front's statements</h1>
    <form method="GET" action="" id="frm-filter" class="filter">
        <div>
            <label for="from_date" >From: </label>
            <input type="date" id="from_date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
            <label for="to_date">To: </label>
            <input type="date" id="to_date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
            <label for="amount_range">Amount range:</label>
            <select id="amount_range" name="amount_range" oninput="updateSortOption()" >
                <option value="all" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == 'all') ? 'selected' : ''; ?>>All range (VND)</option>
                <option value="0-100000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '0-100000') ? 'selected' : ''; ?>>0 - 100,000 VND</option>
                <option value="100000-500000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '100000-500000') ? 'selected' : ''; ?>>100,000 - 500,000 VND</option>
                <option value="500000-1000000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '500000-1000000') ? 'selected' : ''; ?>>500,000 - 1,000,000 VND</option>
                <option value="1000000-5000000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '1000000-5000000') ? 'selected' : ''; ?>>1,000,000 - 5,000,000 VND</option>
                <option value="5000000-10000000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '5000000-10000000') ? 'selected' : ''; ?>>5,000,000 - 10,000,000 VND</option>
                <option value="10000000-50000000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '10000000-50000000') ? 'selected' : ''; ?>>10,000,000 - 50,000,000 VND</option>
                <option value="50000000" <?php echo (isset($_GET['amount_range']) && $_GET['amount_range'] == '50000000') ? 'selected' : ''; ?>>Over 50,000,000 VND</option>
            </select>
        </div>
        <div >
            <label for="sort_by">Sort by:</label>
            <select id="sort_by" name="sort_by">
                <option value="date" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'date') ? 'selected' : ''; ?>>Date</option>
                <option value="credit" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'credit') ? 'selected' : ''; ?>>Amount</option>
            </select>    
            <label for="order">Order:</label>
            <select id="order" name="order">
                <option value="asc" <?php echo (isset($_GET['order']) && $_GET['order'] == 'asc') ? 'selected' : ''; ?>>Ascending</option>
                <option value="desc" <?php echo (isset($_GET['order']) && $_GET['order'] == 'desc') ? 'selected' : ''; ?>>Descending</option>
            </select> 
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" placeholder="Enter search content" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" name="filter">SEARCH</button>
            <button type="button" onclick="window.location.href='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>'">CLEAR</button>
        </div>
    </form>

    <?php
    $sql = "SELECT * FROM $tbl_name WHERE 1=1";  
    // Lay gia tri tu form
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
    $amount_range = $_GET['amount_range'];
    $sort_by = $_GET['sort_by'];
    $order = $_GET['order'];
    // Mac dinh gia tri nho nhat va lon nhat
    $min_amount = 0;
    $max_amount = PHP_INT_MAX;
    // Tach gia tri neu amount_range chua dau '-'
    if (strpos($amount_range, '-') !== false) {
        list($min_amount, $max_amount) = explode('-', $amount_range);
    } elseif ($amount_range !== 'all') {
        // Neu ko co dau '-' va ko phai 'all', coi gia tri la so toi thieu
        $min_amount = $amount_range;
    }
    if (!empty($min_amount)) {
        $sql .= " AND credit >= $min_amount";
    }
    if (!empty($max_amount)) {
        $sql .= " AND credit <= $max_amount";
    }
    // Them dkien ngay neu co
    if (!empty($from_date)) {
        $sql .= " AND date >= '$from_date'";
    }
    if (!empty($to_date)) {
        $sql .= " AND date <= '$to_date'";
    }
    // SEARCH
    // Lay tu khoa tim kiem tu form
    $SearchKeyword = isset($_SESSION['search']) ? $_SESSION['search'] : '';
    if($SearchKeyword !== '') {
        $sql .= " AND detail LIKE '%$SearchKeyword%'";   
        echo "<h4>Search results for \"$SearchKeyword\"</h4>";
    }
    // Xdinh tieu chi sap xep
    if ($sort_by === 'date') { // Mac dinh chon 'Date'
        $sql .= " ORDER BY trans_no ";
    }
    if ($sort_by === 'credit') { // Neu ng dung chon 'Amount'
        $sql .= " ORDER BY credit ";
    }

    if ($order === 'desc') {
        $sql .= " DESC ";
    }
   
    // Execute the query
    $result = $conn->query($sql);
    // Count the number of results
    $total_rows = $result->num_rows;
    $time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],4);
    // Display the count of matching results
    if ($total_rows >= 0) {
        echo "<p> <span class='Results'>Results: </span> $total_rows in $time seconds\n</p>";
    }
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $_DEFAUTLIMIT;  
    // Lay trang hien tai tu URL (mac dinh la 1)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : $_MINPAGE;

    // Tinh tong so trang
    $total_pages = ceil($total_rows / $limit);
    if($page > $total_pages){
        $page = $total_pages;
    }
    $page = max($page, 1); // Dam bao trang lon hon hoac bang 1 
    // Tinh chi so bat dau cho LIMIT trong SQL
    $start = ($page - 1) * $limit;
    // Lay du lieu cho trang hien tai
    $sql = "$sql LIMIT $start, $limit";
    $result = $conn->query($sql); 
    
    $nav = '&from_date='.$from_date.'&to_date='.$to_date.'&amount_range='.$amount_range.'&sort_by='.$sort_by.'&order='.$order.'&search='.urlencode($SearchKeyword);
    //Page navigation
    echo '<form method="GET" action="">';
    echo '<input type="hidden" name="from_date" value= "'.$from_date.'">';
    echo '<input type="hidden" name="to_date" value= "'.$to_date.'">';
    echo '<input type="hidden" name="amount_range" value= "'.$amount_range.'">';
    echo '<input type="hidden" name="sort_by" value= "'.$sort_by .'">';
    echo '<input type="hidden" name="order" value= "'.$order .'">';
    echo '<input type="hidden" name="search" value= "'.$SearchKeyword.'">';

    // Nut "First" va "Previous"
    if ($page > 1) {
        echo '<a href="?'. $nav.'&page=1&limit=' . $limit . '"><i class="fa fa-angle-double-left" style="font-size:24px"></i></a>';
        echo '<a href="?'. $nav.'&page=' . ($page - 1) . '&limit=' . $limit . '"><i class="fa fa-angle-left" style="font-size:24px"></i></a>';
    }
    // Menu chon trang
    echo '<select  name="page" onchange="this.form.submit()">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $selected = ($i == $page) ? 'selected' : '';
        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
    }
    echo '</select>';
    // Nut "Next" va "Last"
    if ($page < $total_pages) {
        echo '<a href="?'. $nav.'&page=' . ($page + 1) . '&limit=' . $limit . '"><i class="fa fa-angle-right" style="font-size:24px"></i></a>';
        echo '<a href="?'. $nav.'&page=' . $total_pages . '&limit=' . $limit . '"><i class="fa fa-angle-double-right" style="font-size:24px"></i></a>';
    }
    // Chon so luong hang hien thi tren moi trang
    echo ' | Number of rows: ';
    echo '<select name="limit" onchange="this.form.submit()">';
    foreach ($_OPTIONS as $option) {
        $selected = ($limit == $option) ? 'selected' : '';
        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
    }
    echo '</select>';
    echo '</form>';

    // Hien thi du lieu bang HTML
    echo '<table class="table table-striped table-hover table-bordered border-primary" cellpadding="5">';
    echo '<tr class="table-warning table-bordered border-primary">
        <th class="table-warning table-bordered border-primary" >Trans No</th>
        <th class="table-warning table-bordered border-primary" >Doc No</th>
        <th class="table-warning table-bordered border-primary" >Date</th>
        <th class="table-warning table-bordered border-primary" >Credit</th>
        <th class="table-warning table-bordered border-primary" >Debit</th>
        <th class="table-warning table-bordered border-primary" >Detail</th>
        </tr>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr class="table-bordered border-primary">';
                echo '<td class="table-bordered border-primary align-right">' . $row['trans_no'] . '</td>';
                echo '<td class="table-bordered border-primary align-right">' . $row['doc_no'] . '</td>';
                echo '<td>' . date("d/m/Y", strtotime($row['date'])) . '</td>';
                echo '<td class="table-bordered border-primary align-right">' . number_format($row['credit']) . '</td>';
                echo '<td class="table-bordered border-primary align-right">' . $row['debit'] . '</td>';
                echo '<td class="table-bordered border-primary">' . $row['detail'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr class="table-bordered border-primary"><td  class="table-bordered border-primary" colspan="10">No results</td></tr>';
    }
    echo '</table>';

    //Page navigation
    echo '<form method="GET" action="">';
    echo '<input type="hidden" name="from_date" value= "'.$from_date.'">';
    echo '<input type="hidden" name="to_date" value= "'.$to_date.'">';
    echo '<input type="hidden" name="amount_range" value= "'.$amount_range.'">';
    echo '<input type="hidden" name="sort_by" value= "'.$sort_by .'">';
    echo '<input type="hidden" name="order" value= "'.$order .'">';
    echo '<input type="hidden" name="search" value= "'.$SearchKeyword.'">';

    // Nut "First" va "Previous"
    if ($page > 1) {
        echo '<a href="?'. $nav.'&page=1&limit=' . $limit . '"><i class="fa fa-angle-double-left" style="font-size:24px"></i></a>';
        echo '<a href="?'. $nav.'&page=' . ($page - 1) . '&limit=' . $limit . '"><i class="fa fa-angle-left" style="font-size:24px"></i></a>';
    }
    // Menu chon trang
    echo '<select  name="page" onchange="this.form.submit()">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $selected = ($i == $page) ? 'selected' : '';
        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
    }
    echo '</select>';
    // Nut "Next" va "Last"
    if ($page < $total_pages) {
        echo '<a href="?'. $nav.'&page=' . ($page + 1) . '&limit=' . $limit . '"><i class="fa fa-angle-right" style="font-size:24px"></i></a>';
        echo '<a href="?'. $nav.'&page=' . $total_pages . '&limit=' . $limit . '"><i class="fa fa-angle-double-right" style="font-size:24px"></i></a>';
    }
    // Chon so luong hang hien thi tren moi trang
    echo ' | Number of rows: ';
    echo '<select name="limit" onchange="this.form.submit()">';
    foreach ($_OPTIONS as $option) {
        $selected = ($limit == $option) ? 'selected' : '';
        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
    }
    echo '</select>';
    echo '</form>';

    // Dong ket noi
    $conn->close();
    ?>

    <footer class="footer-32892 pb-0">
        <div class="row">
            <p class="Design-By">Created by Group <span class="T505">T505</span></p>
        </div>
    </footer>
</body>
</html> 
<script src="<?php echo _WEB_HOST;?>/templates/js/bootstrap.min.js"></script>
<script src="<?php echo _WEB_HOST;?>/templates/js/custom.js"></script>


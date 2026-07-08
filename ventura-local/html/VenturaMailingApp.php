<?php include "../inc/dbinfo.inc"; ?>
<html>
<head>
<style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 40px 20px;
    color: #2c3e50;
  }

  h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    font-weight: 600;
  }

  .card {
    background: #ffffff;
    max-width: 700px;
    margin: 0 auto 30px auto;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  form table {
    width: 100%;
    border-collapse: collapse;
  }

  form td {
    padding: 8px 10px;
    font-size: 14px;
    font-weight: 500;
    color: #555;
  }

  input[type="text"] {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
  }

  input[type="text"]:focus {
    outline: none;
    border-color: #6c8ebf;
  }

  input[type="submit"] {
    background-color: #4a90e2;
    color: #ffffff;
    border: none;
    padding: 9px 18px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    margin-top: 5px;
  }

  input[type="submit"]:hover {
    background-color: #3b78c2;
  }

  table.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }

  table.data-table th {
    background-color: #4a90e2;
    color: #ffffff;
    text-align: left;
    padding: 10px 12px;
  }

  table.data-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e5e7eb;
  }

  table.data-table tr:nth-child(even) {
    background-color: #f9fafb;
  }

  table.data-table tr:hover {
    background-color: #f1f5f9;
  }
</style>
</head>
<body>
<h1>Ventura Mailing Service</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the EMPLOYEES table exists. */
  VerifyEmployeesTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the EMPLOYEES table. */
  $employee_name = htmlentities($_POST['NAME']);
  $employee_address = htmlentities($_POST['ADDRESS']);

  if (strlen($employee_name) || strlen($employee_address)) {
    AddEmployee($connection, $employee_name, $employee_address);
  }
?>

<!-- Input form -->
<div class="card">
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>ADDRESS</td>
      <td></td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>
</div>

<!-- Display table data. -->
<div class="card">
<table class="data-table">
  <tr>
    <th>ID</th>
    <th>NAME</th>
    <th>ADDRESS</th>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>";
  echo "</tr>";
}
?>

</table>
</div>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
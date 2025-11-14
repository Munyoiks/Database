<?php include '../connection/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Finance Management</title>
</head>
<body class="bg-gray-50 flex">
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Finance Management</h1>
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
        <i class="fas fa-plus mr-2"></i> Add Payment
      </button>
    </div>

    <!-- Finance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Total Revenue</h3>
            <?php 
              $r = $conn->query("SELECT SUM(amount_paid) AS revenue FROM finance");
              $revenue = $r->fetch_assoc()['revenue'] ?? 0;
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2">KSh <?= number_format($revenue) ?></p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-red-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Pending Payments</h3>
            <?php 
              $r = $conn->query("SELECT SUM(balance) AS pending FROM finance");
              $pending = $r->fetch_assoc()['pending'] ?? 0;
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2">KSh <?= number_format($pending) ?></p>
          </div>
          <div class="bg-red-100 p-3 rounded-lg">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Payment Rate</h3>
            <?php 
              $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM finance");
              $paidQuery = $conn->query("SELECT COUNT(*) AS paid FROM finance WHERE balance = 0");
              $total = $totalQuery->fetch_assoc()['total'] ?? 0;
              $paid = $paidQuery->fetch_assoc()['paid'] ?? 0;
              $rate = $total > 0 ? round(($paid / $total) * 100) : 0;
            ?>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $rate ?>%</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <i class="fas fa-chart-line text-blue-600 text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Records -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Payment Records</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php
            $result = $conn->query("SELECT * FROM finance LIMIT 10");
            if ($result && $result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                $status = $row['balance'] == 0 ? 'Paid' : 'Pending';
                $statusClass = $row['balance'] == 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                
                echo '
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">'.$row['student_name'].'</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">'.$row['invoice_number'].'</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    KSh '.number_format($row['amount_due']).'
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    KSh '.number_format($row['amount_paid']).'
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    KSh '.number_format($row['balance']).'
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    '.$row['due_date'].'
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full '.$statusClass.'">
                      '.$status.'
                    </span>
                  </td>
                </tr>
                ';
              }
            } else {
              echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No finance records found</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
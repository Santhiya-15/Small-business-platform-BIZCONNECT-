<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

require_once 'config.php';

// Handle mark as read action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark_read' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE contact_messages SET status = 'read' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM contact_messages WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch messages with filtering
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_clause = '';
if ($filter === 'unread') {
    $where_clause = "WHERE status = 'unread'";
} elseif ($filter === 'read') {
    $where_clause = "WHERE status = 'read'";
}

if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    if (!empty($where_clause)) {
        $where_clause .= " AND (name LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR message LIKE '%$search_escaped%')";
    } else {
        $where_clause = "WHERE (name LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR message LIKE '%$search_escaped%')";
    }
}

// Get total messages and unread count
$count_sql = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) as unread FROM contact_messages";
$count_result = $conn->query($count_sql);
$counts = $count_result->fetch_assoc();

// Fetch messages ordered by newest first
$sql = "SELECT id, name, email, message, created_at, status FROM contact_messages $where_clause ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BIZCONNECT Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(14,26,43,0.88), rgba(14,26,43,0.88)),
                        url("https://images.unsplash.com/photo-1497215728101-856f4ea42174");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            min-height: 100vh;
        }
        
        .admin-header {
            background: rgba(17, 24, 39, 0.95);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-header h1 {
            font-size: 24px;
        }
        
        .admin-header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-header .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .admin-header .logout-btn:hover {
            background: #dc2626;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(17, 24, 39, 0.95);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            text-align: center;
        }
        
        .stat-card h3 {
            color: #9ca3af;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
        }
        
        .filters-section {
            background: rgba(17, 24, 39, 0.95);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filters-section form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filters-section input {
            padding: 10px 15px;
            border: 1px solid #374151;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }
        
        .filters-section input::placeholder {
            color: #9ca3af;
        }
        
        .filters-section select {
            padding: 10px 15px;
            border: 1px solid #374151;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }
        
        .filters-section option {
            background: #1f2937;
            color: white;
        }
        
        .filters-section button {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .filters-section button:hover {
            background: #5568d3;
        }
        
        .reset-btn {
            background: #6b7280 !important;
        }
        
        .reset-btn:hover {
            background: #4b5563 !important;
        }
        
        .messages-container {
            background: rgba(17, 24, 39, 0.95);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .message-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            transition: background 0.2s;
        }
        
        .message-item:hover {
            background: rgba(107, 114, 128, 0.2);
        }
        
        .message-item.unread {
            background: rgba(99, 102, 241, 0.15);
            border-left: 4px solid #667eea;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
        }
        
        .message-info {
            flex: 1;
        }
        
        .message-info h3 {
            margin-bottom: 4px;
            font-size: 16px;
        }
        
        .message-info p {
            color: #9ca3af;
            font-size: 13px;
            margin-bottom: 4px;
        }
        
        .message-timestamp {
            color: #6b7280;
            font-size: 12px;
        }
        
        .message-text {
            color: #d1d5db;
            margin: 12px 0;
            line-height: 1.6;
            padding: 12px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
        }
        
        .message-actions {
            display: flex;
            gap: 10px;
        }
        
        .message-actions form {
            display: inline;
        }
        
        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }
        
        .mark-read-btn {
            background: #3b82f6;
            color: white;
        }
        
        .mark-read-btn:hover {
            background: #2563eb;
        }
        
        .delete-btn {
            background: #ef4444;
            color: white;
        }
        
        .delete-btn:hover {
            background: #dc2626;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .badge-unread {
            background: #667eea;
            color: white;
        }
        
        .badge-read {
            background: #6b7280;
            color: white;
        }
        
        .no-messages {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        
        .no-messages i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #6b7280;
        }
        
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .message-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .filters-section {
                flex-direction: column;
            }
            
            .filters-section form {
                width: 100%;
                flex-direction: column;
            }
            
            .filters-section input,
            .filters-section select,
            .filters-section button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-envelope"></i> Messages Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <h3>Total Messages</h3>
                <div class="number"><?php echo $counts['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Unread Messages</h3>
                <div class="number"><?php echo $counts['unread'] ?? 0; ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
                <input type="text" name="search" placeholder="Search by name, email, or message..." 
                       value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; min-width: 250px;">
                
                <select name="filter">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Messages</option>
                    <option value="unread" <?php echo $filter === 'unread' ? 'selected' : ''; ?>>Unread Only</option>
                    <option value="read" <?php echo $filter === 'read' ? 'selected' : ''; ?>>Read Only</option>
                </select>
                
                <button type="submit"><i class="fas fa-search"></i> Search</button>
                <a href="admin.php" class="reset-btn" style="padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;">Reset</a>
            </form>
        </div>
        
        <!-- Messages List -->
        <div class="messages-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($message = $result->fetch_assoc()): ?>
                    <div class="message-item <?php echo $message['status'] === 'unread' ? 'unread' : ''; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <h3>
                                    <?php echo htmlspecialchars($message['name']); ?>
                                    <span class="badge badge-<?php echo $message['status']; ?>">
                                        <?php echo strtoupper($message['status']); ?>
                                    </span>
                                </h3>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email']); ?></p>
                                <span class="message-timestamp">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo date('F j, Y \a\t g:i A', strtotime($message['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="message-text">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                        
                        <div class="message-actions">
                            <?php if ($message['status'] === 'unread'): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" class="action-btn mark-read-btn">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                <button type="submit" class="action-btn delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-messages">
                    <i class="fas fa-inbox"></i>
                    <p>No messages found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Handle logout
        document.querySelectorAll('button[name="logout"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                <?php
                    session_destroy();
                ?>
                window.location.href = 'admin_login.php';
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>

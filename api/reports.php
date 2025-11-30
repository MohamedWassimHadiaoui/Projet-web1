
```php
<?php
// Include database connection from parent directory
require_once '../config.php';

// Get HTTP method (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// ========================================
// CREATE - POST REQUEST
// ========================================
if ($method === 'POST') {
    // Get JSON data from request
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if we got valid data
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }
    
    // Extract fields from data
    $type = $data['type'] ?? '';
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $location = $data['location'] ?? '';
    $incident_date = $data['incident_date'] ?? NULL;
    $priority = $data['priority'] ?? 'medium';
    
    // Validate required fields
    if (empty($type) || empty($title) || empty($description)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields: type, title, description']);
        exit;
    }
    
    // Escape strings for security (prevent SQL injection)
    $type = mysqli_real_escape_string($conn, $type);
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $location = mysqli_real_escape_string($conn, $location);
    $priority = mysqli_real_escape_string($conn, $priority);
    
    // Build SQL INSERT query
    $sql = "INSERT INTO reports (type, title, description, location, incident_date, priority) 
            VALUES ('$type', '$title', '$description', '$location', '$incident_date', '$priority')";
    
    // Execute query
    if (mysqli_query($conn, $sql)) {
        // Get the ID of newly created report
        $newId = mysqli_insert_id($conn);
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Report created successfully',
            'id' => $newId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit;
}

// ========================================
// READ ALL - GET REQUEST (no ID)
// ========================================
elseif ($method === 'GET' && !isset($_GET['id'])) {
    // Build SQL SELECT query to get ALL reports
    $sql = "SELECT * FROM reports ORDER BY created_at DESC";
    
    // Execute query
    $result = mysqli_query($conn, $sql);
    
    // Convert results to array
    $reports = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reports[] = $row;
    }
    
    // Send response
    echo json_encode([
        'success' => true,
        'count' => count($reports),
        'data' => $reports
    ]);
    exit;
}

// ========================================
// READ ONE - GET REQUEST (with ID)
// ========================================
elseif ($method === 'GET' && isset($_GET['id'])) {
    // Get ID from URL
    $id = intval($_GET['id']);
    
    // Build SQL SELECT query for specific report
    $sql = "SELECT * FROM reports WHERE id = $id";
    
    // Execute query
    $result = mysqli_query($conn, $sql);
    
    // Check if found
    if ($result && mysqli_num_rows($result) > 0) {
        // Convert to array
        $report = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'data' => $report]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Report not found']);
    }
    exit;
}

// ========================================
// UPDATE - PUT REQUEST
// ========================================
elseif ($method === 'PUT') {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if ID is provided
    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID is required']);
        exit;
    }
    
    // Get ID and new values
    $id = intval($data['id']);
    $type = $data['type'] ?? '';
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $location = $data['location'] ?? '';
    $status = $data['status'] ?? 'pending';
    $priority = $data['priority'] ?? 'medium';
    
    // Validate required fields
    if (empty($type) || empty($title) || empty($description)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    // Escape strings
    $type = mysqli_real_escape_string($conn, $type);
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $location = mysqli_real_escape_string($conn, $location);
    $status = mysqli_real_escape_string($conn, $status);
    $priority = mysqli_real_escape_string($conn, $priority);
    
    // Build SQL UPDATE query
    $sql = "UPDATE reports 
            SET type='$type', title='$title', description='$description', 
                location='$location', status='$status', priority='$priority' 
            WHERE id=$id";
    
    // Execute query
    if (mysqli_query($conn, $sql)) {
        // Check if any rows were updated
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(['success' => true, 'message' => 'Report updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Report not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit;
}

// ========================================
// DELETE - DELETE REQUEST
// ========================================
elseif ($method === 'DELETE') {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if ID is provided
    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID is required']);
        exit;
    }
    
    // Get ID
    $id = intval($data['id']);
    
    // Build SQL DELETE query
    $sql = "DELETE FROM reports WHERE id = $id";
    
    // Execute query
    if (mysqli_query($conn, $sql)) {
        // Check if any rows were deleted
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(['success' => true, 'message' => 'Report deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Report not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit;
}

// ========================================
// Invalid request
// ========================================
else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Close connection
mysqli_close($conn);
?>
```

---

## ✅ HOW TO CREATE reports.php

### Step 1: Create api folder
1. Open `C:\xampp\htdocs\PeaceConnect\`
2. Right-click → New → Folder
3. Name it: `api`

### Step 2: Create reports.php
1. Open text editor (Notepad, VS Code, etc.)
2. Copy-paste the **ENTIRE code above**
3. Save as: `reports.php`
4. Location: `C:\xampp\htdocs\PeaceConnect\api\reports.php`

---

## 🧪 TEST YOUR CRUD

### Test 1: GET ALL REPORTS
```
URL: http://localhost/PeaceConnect/api/reports.php
Method: GET
Response: {"success":true,"count":0,"data":[]}
```

### Test 2: CREATE A REPORT
```
URL: http://localhost/PeaceConnect/api/reports.php
Method: POST
Body (JSON):
{
    "type": "conflict",
    "title": "Test Report",
    "description": "This is a test report",
    "location": "Test Location"
}

Response: {"success":true,"message":"Report created successfully","id":1}
```

### Test 3: GET ONE REPORT
```
URL: http://localhost/PeaceConnect/api/reports.php?id=1
Method: GET
Response: {"success":true,"data":{...report data...}}
```

### Test 4: UPDATE REPORT
```
URL: http://localhost/PeaceConnect/api/reports.php
Method: PUT
Body (JSON):
{
    "id": 1,
    "type": "harassment",
    "title": "Updated Title",
    "description": "Updated description",
    "location": "Updated Location",
    "status": "assigned",
    "priority": "high"
}

Response: {"success":true,"message":"Report updated successfully"}
```

### Test 5: DELETE REPORT
```
URL: http://localhost/PeaceConnect/api/reports.php
Method: DELETE
Body (JSON):
{
    "id": 1
}

Response: {"success":true,"message":"Report deleted successfully"}
```

---

## 🎯 TESTING IN BROWSER CONSOLE

Open browser (Chrome, Firefox) and press F12 to open console.

**Test CREATE:**
```javascript
fetch('http://localhost/PeaceConnect/api/reports.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        type: 'conflict',
        title: 'My Report',
        description: 'Description here',
        location: 'Location here'
    })
})
.then(r => r.json())
.then(d => console.log(d))
```

**Test READ ALL:**
```javascript
fetch('http://localhost/PeaceConnect/api/reports.php')
.then(r => r.json())
.then(d => console.log(d))
```

**Test UPDATE:**
```javascript
fetch('http://localhost/PeaceConnect/api/reports.php', {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        id: 1,
        type: 'harassment',
        title: 'Updated',
        description: 'Updated desc',
        location: 'Updated loc',
        status: 'assigned',
        priority: 'high'
    })
})
.then(r => r.json())
.then(d => console.log(d))
```

**Test DELETE:**
```javascript
fetch('http://localhost/PeaceConnect/api/reports.php', {
    method: 'DELETE',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id: 1})
})
.then(r => r.json())
.then(d => console.log(d))
```

---


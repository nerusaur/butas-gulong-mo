<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}
// Access session variables
$name = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : "Guest";
echo "<script>alert('Welcome, " . $name . "');</script>";

$user_id = $_SESSION['user_id'];
// Handle diary entry submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_entry'])) {
  $mood = $_POST['mood'];
  $title = $_POST['title'];
  $entry = $_POST['entry'];
  $gratitude = $_POST['gratitude'];
  $image_path = null;

  // Handle image upload
  if (!empty($_FILES['image']['name'])) {
      $image_path = 'uploads/' . basename($_FILES['image']['name']);
      move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

    // Insert entry into the database
    $stmt = $conn->prepare("INSERT INTO diary_entries (user_id, mood, title, entry, gratitude, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $mood, $title, $entry, $gratitude, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Entry added successfully!');</script>";
    } else {
        echo "<script>alert('Failed to add entry.');</script>";
    }
    $stmt->close();
}




// Handle logout action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  session_unset(); // Remove all session variables
  session_destroy(); // Destroy the session

  echo "<script>
      alert('You have been logged out.');
      window.location.href = 'index.php';
  </script>";
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Diary</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600;700&family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="Home.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <nav>
    <!-- nav header -->
    <div class="nav-header">
      <p>Dear Diary</p>
    </div>
    <!-- links -->
    <ul class="links">
      <li>
        <a href="#home" id="txt">home</a>
      </li>
      <li>
        <a href="#create"id="txt">Create</a>
      </li>
      <li>
        <a href="#previous"id="txt">Previous</a>
      </li>
      <li>
        <a href="#moods"id="txt">Moods</a>
      </li>
      <li><form method="POST" action="home.php">
      <a href="#logout"id="txt"><button type="submit" name="logout" class="logout-button">Logout</button></a>
    </form>
      </li>
    </ul>
    <button class="nav-toggle">
      <i class="fas fa-bars"></i>
    </button>
  </nav>
  
  <section id="home">
    <h1>Write your Story</h1>
    <p>One click at a time</p>
    <a href="#create" class="btn-start"><h2>Create!</h1></a>
  </section>
  <br> <br> <br> <br>
  <section id="create">
    <div class="container">
        <!-- New Entry Section -->
        <div class="box">
            <section id="section-new" class="section-new">
                <h1>New Entry</h1>
                <form autocomplete="off" id="entryForm">
                    <select id="mood-selector" class="mood-dropdown">
                        <option value="" disabled selected>Select Mood</option>
                        <option value="Happy">Happy 😊</option>
                        <option value="Relaxed">Relaxed 😌</option>
                        <option value="Focused">Focused 🎯</option>
                        <option value="Excited">Excited 😄</option>
                        <option value="Calm">Calm 🌊</option>
                        <option value="Sad">Sad 😢</option>
                        <option value="Anxious">Anxious 😰</option>
                        <option value="Angry">Angry 😠</option>
                        <option value="Content">Content 😊</option>
                        <option value="Energetic">Energetic ⚡</option>
                    </select>
                    <input type="text" name="entry-title" id="entry-title" class="entry-title" placeholder="Title 🖊️" />
                    <textarea name="daily-entry" id="entry" class="entry-box" placeholder="Dear Diary ...🖊️"></textarea>
                    <button type="submit" id="submit">Add To Diary</button>
                </form>
            </section>
        </div>

        <!-- I'm Grateful For Section -->
        <div class="box">
            <section class="optional">
                <h1>I am grateful for:</h1>
                <form>
                    <input type="text" name="gratitude" id="entry-gratitude" class="entry-gratitude" placeholder="I am thankful for... 🖊️" />
                    
                        <label for="image-upload" class="image-button" id="image-container">
                            <img src="img/insert-photo-here.png" alt="Upload Image" id="preview-image" />
                        </label>
                        <input type="file" id="image-upload" accept="image/*" style="display: none;" />
                    
                </form>
            </section>
        </div>
    </div>
</section>


      <section class="previous">
        <div class="full-height-container">
          <!-- Previous Entries Section -->
          <section class="section-old" id="entryResultsSection">
            <h1>Previous Entries</h1>
          <select id="mood-selector-filter" class="mood-dropdown-filter">
                  <option value="" disabled selected>Filter</option>
                  <option value="Happy">Happy 😊</option>
                  <option value="Relaxed">Relaxed 😌</option>
                  <option value="Focused">Focused 🎯</option>
                  <option value="Excited">Excited 😄</option>
                  <option value="Calm">Calm 🌊</option>
                  <option value="Sad">Sad 😢</option>
                  <option value="Anxious">Anxious 😰</option>
                  <option value="Angry">Angry 😠</option>
                  <option value="Content">Content 😊</option>
                  <option value="Energetic">Energetic ⚡</option>
                  <option value="Date">By Date</option>
                  <option value="None">None</option>
                </select>
                <div class="history"></div>
            <div class="container">
              

            </div>
          </section>
        </div>
      </main>
    </section>
  </section>
  <br> <br> <br> <br>
  <section id="moods">
    <section class="mood-chart-section">
      <h2>Mood Chart</h2>
      <h3>This graph shows your mood based on your entries</h3>
      <div class="chart-container">
        
        <canvas id="moodChart"></canvas>
        <hr>
        <p id="mood-percentage"></p>
      </div>
    </section>
      </div>
          
  </section>
  
  <script src="index.js"></script>
  
</body>
</html>
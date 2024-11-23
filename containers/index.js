const navToggle = document.querySelector(".nav-toggle");
const links = document.querySelector(".links");

navToggle.addEventListener("click", function () {
  links.classList.toggle("show");
});


// Theme change function
function changeTheme() {
  const theme = document.getElementById("theme-select").value;
  document.body.classList.remove("light-theme",
     "dark-theme",
      "neutral-theme",
       "pastel-theme",
       "glassify-theme",
        "tropical-theme");
  document.body.classList.add(`${theme}-theme`);
}


document.addEventListener('DOMContentLoaded', function () {
  // Elements for login and registration forms
  const entryForm = document.getElementById('entryForm');
  const entryResultRow = document.querySelector('.history');
  const getEntryTitle = document.getElementById('entry-title');
  const getEntryText = document.getElementById('entry');
  const moodSelector = document.getElementById('mood-selector');
  const moodPercentageDisplay = document.getElementById('mood-percentage');
  const imageUpload = document.getElementById('image-upload');
  const previewImage = document.getElementById('preview-image');
  const imageContainer = document.getElementById('image-container');

  let uploadedImageSrc = '';
  const moodCount = {
    Happy: 0,
    Relaxed: 0,
    Focused: 0,
    Excited: 0,
    Calm: 0,
    Sad: 0,
    Anxious: 0,
    Angry: 0,
    Content: 0,
    Energetic: 0
  };
  let moodChart;

  // Function to update mood chart
  function updateMoodChart() {
    const moodData = Object.values(moodCount);
    const ctx = document.getElementById('moodChart').getContext('2d');

    if (!moodChart) {
      moodChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: Object.keys(moodCount),
          datasets: [{
            data: moodData,
            backgroundColor: ['#FFD700', '#1E90FF', '#2E8B57', '#FF69B4', '#87CEEB', '#A9A9A9', '#FF4500', '#FF0000', '#32CD32', '#FFFF00']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              labels: { color: '#FFFFFF' }
            }
          }
        }
      });
    } else {
      moodChart.data.datasets[0].data = moodData;
      moodChart.update();
    }

    updateMostFrequentMoodPercentage();
  }

  function updateMostFrequentMoodPercentage() {
    const totalMoods = Object.values(moodCount).reduce((acc, count) => acc + count, 0);
    if (totalMoods === 0) {
      moodPercentageDisplay.textContent = "No mood data available";
      return;
    }

    const mostFrequentMood = Object.keys(moodCount).reduce((a, b) => moodCount[a] > moodCount[b] ? a : b);
    const mostFrequentMoodCount = moodCount[mostFrequentMood];
    const percentage = ((mostFrequentMoodCount / totalMoods) * 100).toFixed(2);

    moodPercentageDisplay.textContent = `Most Frequent Mood: ${mostFrequentMood} (${percentage}%)`;
  }

  // Handle image upload and preview
  imageUpload.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        uploadedImageSrc = e.target.result; // Ensure this updates correctly
        console.log("Image uploaded:", uploadedImageSrc); // Debugging
        previewImage.src = uploadedImageSrc;
        imageContainer.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  });

  // Function to add a new entry
  function addEntry(event) {
    event.preventDefault();

    const entryTitle = getEntryTitle.value.trim();
    const entryText = getEntryText.value.trim();
    const selectedMood = moodSelector.value;

    if (!entryTitle || !entryText || !selectedMood) {
      alert("Please fill in the title, entry, and select a mood.");
      return;
    }

    // Update mood count
    if (selectedMood && moodCount[selectedMood] !== undefined) {
      moodCount[selectedMood]++;
      updateMoodChart();
    }

    const d = new Date();
    const dateString = `${d.getDate()} ${d.toLocaleString('default', { month: 'long' })} ${d.getFullYear()}`;

    // Create entry container
    const entryBox = document.createElement('div');
    entryBox.className = 'entry-box-container';
    entryBox.style.cursor = 'pointer';
    entryBox.style.border = '1px solid #ccc';
    entryBox.style.borderRadius = '5px';
    entryBox.style.padding = '10px';
    entryBox.style.margin = '10px auto';
    entryBox.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';

    // Truncate text with ellipsis
     entryBox.style.overflow = "hidden";
    entryBox.style.textOverflow = "ellipsis";
    entryBox.style.whiteSpace = "nowrap"; // Keep text in a single line

    const entryHeading = document.createElement('h3');
    entryHeading.textContent = entryTitle;
    entryBox.appendChild(entryHeading);

    const entryDate = document.createElement('p');
    entryDate.textContent = `Date Added: ${dateString}`;
    entryDate.style.fontStyle = 'italic';
    entryBox.appendChild(entryDate);

    const entryContent = document.createElement('p');
    entryContent.textContent = entryText;
    entryBox.appendChild(entryContent);

    // Bind image source to the entry
  const currentImageSrc = uploadedImageSrc; // Capture current image

    // Add the image if uploaded
    if (uploadedImageSrc) {
      const entryImage = document.createElement('img');
      entryImage.src = uploadedImageSrc;
      entryImage.style.width = '100%';
      entryImage.style.maxHeight = '150px';
      entryImage.style.objectFit = 'cover';
      entryBox.appendChild(entryImage);
    }

    entryResultRow.appendChild(entryBox);

 // Open entry in new tab with image and content
entryBox.addEventListener('click', function () {
  const newTab = window.open('', '_blank');
  if (newTab) {
    const imageHTML = currentImageSrc
      ? `<img src="${currentImageSrc}" style="max-width: 100%; max-height: 400px; object-fit: cover;">`
      : '';

    // Ensure entryText is properly escaped for inclusion in the script
    const escapedEntryText = JSON.stringify(entryText);

    newTab.document.write(`
<!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>${entryTitle}</title>
        <style>
          body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background-color: #fefcf3;
            color: #4a4a4a;
            line-height: 1.6;
          }
          .diary {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
          }
          h1 {
            font-family: 'Lucida Handwriting', cursive;
            font-size: 2em;
            text-align: center;
            margin-bottom: 10px;
            color: #333;
          }
          .date {
            font-size: 0.9em;
            text-align: right;
            color: #666;
            margin-bottom: 20px;
          }
          img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 20px auto;
            border: 2px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
          }
          .entry-text {
            font-size: 1.1em;
            text-indent: 2em;
            text-align: justify;
            margin-top: 10px;
            white-space: pre-wrap;
          }
          .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #999;
          }

          /* Responsive Styles */
          @media (max-width: 768px) {
            .diary {
              margin: 20px 10px;
              padding: 15px;
            }
            h1 {
              font-size: 1.8em;
            }
            .entry-text {
              font-size: 1em;
              text-indent: 1.5em;
            }
          }

          @media (max-width: 480px) {
            .diary {
              margin: 10px 5px;
              padding: 10px;
            }
            h1 {
              font-size: 1.5em;
            }
            .entry-text {
              font-size: 0.9em;
              text-indent: 1em;
            }
            .date {
              font-size: 0.8em;
            }
          }
        </style>
      </head>
      <body>
        <div class="diary">
          <h1>${entryTitle}</h1>
          <p class="date"><em>${dateString}</em></p>
          ${imageHTML}
          <p class="entry-text" id="entryText"></p>
          <div class="footer">
            <p>“A moment in time, forever etched in words.”</p>
          </div>
        </div>
        <script>
          const text = ${escapedEntryText};
          const entryTextElement = document.getElementById('entryText');
          let i = 0;

          function typeWriter() {
            if (i < text.length) {
              entryTextElement.textContent += text.charAt(i);
              i++;
              setTimeout(typeWriter, 20); // Adjust typing speed (50ms per character)
            }
          }

          typeWriter();
        </script>
      </body>
      </html>
    `);
    newTab.document.close();
  }
});
    // Reset form
    getEntryTitle.value = '';
    getEntryText.value = '';
    moodSelector.value = '';
    uploadedImageSrc = '';
    previewImage.src = 'img/insert-photo-here.png';
    imageContainer.style.display = 'img/insert-photo-here.png';
  }

  entryForm.addEventListener('submit', addEntry);
});

document.addEventListener('DOMContentLoaded', function() {
    loadProjects('loadProjects.php');

    // ability to expand projects for more details
    const projectTilesContainer = document.querySelector('.project-tiles');
    projectTilesContainer.addEventListener('click', function(event) {
        const target = event.target;
        const projectTile = target.closest('.project-tile');
        
        if (projectTile) {
            const details = projectTile.getElementsByClassName('project-details')[0];
            details.style.display = details.style.display === 'none' || !details.style.display ? 'block' : 'none';
        }
    });

    // Search functionality
    document.querySelector('.search').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            document.getElementById('search-projects').click();
        }
    });

    document.getElementById("search-projects").addEventListener("click", function() {
        clearProjects();
        var searchValue = document.querySelector('.search').value;
        var dateValue = document.querySelector('#search-startDate').value;
        loadProjects('search.php?search=' + encodeURIComponent(searchValue) + '&date=' + encodeURIComponent(dateValue));
    });

    document.getElementById("clear-search").addEventListener("click", function() {
        clearProjects();
        document.querySelector('.search').value = '';
        document.getElementById("search-startDate").value = '';
        loadProjects('loadProjects.php');
    });
    
    // Logout/Login button
    if (document.cookie.indexOf('session_id=') >= 0) {
        document.getElementById('loginLink').innerHTML = "Logout";
        document.getElementById('loginLink').href = "http://localhost/Port3/logout.php"
    } else {
        document.getElementById('loginLink').innerHTML = "Login";
    }
});

function clearProjects(){
    var projectTiles = document.querySelectorAll('.project-tiles .project-tile');
    for (var i = 0; i < projectTiles.length; i++) {
        if (!projectTiles[i].classList.contains('plus-tile')) {
            projectTiles[i].parentNode.removeChild(projectTiles[i]);
        }
    }
}

function loadProjects(script) { 
    fetch(script)
    .then(response => response.json())
    .then(data => {
        const projectTiles = document.querySelector('.project-tiles');
        data.forEach(project => {
            const projectTile = document.createElement('div');
            projectTile.classList.add('project-tile');
            projectTile.setAttribute('data-project', project.title);
            projectTile.innerHTML = `
                <div class="project-content">
                    <div class="project-tile-title">
                        <h2>${project.title}</h2>
                        ${project.edit_button}
                    </div>
                    <p><strong>Start Date:</strong> ${project.start_date}</p>
                    <p><strong>Description:</strong> ${project.description}</p>
                    <div class="project-details" style="display: none;">
                        <p><strong>End Date:</strong> ${project.end_date}</p>
                        <p><strong>Email:</strong> ${project.email}</p>
                    </div>
                </div>`;
            projectTiles.appendChild(projectTile);
        });
    })
    .catch(error => console.error('Error fetching projects:', error));
}

function upload() {
    if (document.cookie.indexOf('session_id=') >= 0) {
        window.location.href = 'http://localhost/Port3/upload.php';
    } else {
        alert('Please login to upload a project.');
    }
}

function showUsersProjects(){
    if (document.cookie.indexOf('session_id=') >= 0) {
        clearProjects();
        loadProjects('searchWithUID.php');
    } else {
        alert('Please login to view your projects.');
    }
}

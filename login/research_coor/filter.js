function toggleFilterDropdown() {
    let dropdown = document.getElementById("filterDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

function updateFilterInput() {
    let column = document.getElementById("filterColumn").value;
    let container = document.getElementById("filterInputContainer");
    container.innerHTML = "";
    
    if (column === "notification") {
        container.innerHTML = `
            <select id="filterValue" class="filter-dropdown">
                <option value="">Select Notification</option>
                <option value="For Revision">For Revision</option>
                <option value="Scheduled for Research Proposal Presentation">Scheduled for Research Proposal Presentation</option>
                <option value="Scheduled for Final Research Presentation">Scheduled for Final Research Presentation</option>
                <option value="Please See Comments">Please See Comments</option>
            </select>`;
    } else if (column === "sched_proposal" || column === "sched_final") {
        container.innerHTML = `<input type="date" id="filterValue" class="filter-input">`;
    } else if (column === "research_status") {
        container.innerHTML = `
            <select id="filterValue" class="filter-dropdown">
                <option value="">Select Research Status</option>
                <option value="Presented">Presented</option>
                <option value="Implemented">Implemented</option>
            </select>`;
    } else {
        container.innerHTML = `<input type="text" id="filterValue" class="filter-input" placeholder="Enter value...">`;
    }
}

function applyFilters() {
    let column = document.getElementById("filterColumn").value;
    let value = document.getElementById("filterValue").value.toLowerCase();
    let table = document.getElementById("researchTable");
    let rows = table.getElementsByTagName("tr");
    
    let columnIndex = {
        "notification": 8,
        "sched_proposal": 9,
        "sched_final": 10,
        "research_status": 11
    }[column];
    
    for (let i = 1; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName("td");
        if (columnIndex !== undefined) {
            let cellText = cells[columnIndex].textContent.toLowerCase();
            rows[i].style.display = cellText.includes(value) ? "" : "none";
        }
    }
    document.getElementById("filterDropdown").style.display = "none";
}
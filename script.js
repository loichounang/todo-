function editTask(id, title) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_title").value = title;

    document.getElementById("edit-popup").classList.remove("hidden");
}

function closePopup() {
    document.getElementById("edit-popup").classList.add("hidden");
}

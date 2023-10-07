<div id="popup-container" class="popup-container" style="display: none">
    <h1>Delete <i data-feather="alert-circle"></i></h1>
    <span class="close-icon" onclick="closeDeletePopup()">&#215;</span>
    <div class="form-field">
        <label class="form-label">Are you sure you want to delete this item?</label>
        <div class="form-field"></div>
        <div class="buttons-container">
            <form id="deleteForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
                <button type="button" onclick="closeDeletePopup()">Close</button>
            </form>
        </div>
    </div>
</div>
<script>
    function openDeletePopup(route) {
        var popup = document.getElementById("popup-container");
        var deleteForm = document.getElementById("deleteForm");

        deleteForm.action = route;
        popup.style.display = "block";
    }

    function closeDeletePopup() {
        var popup = document.getElementById("popup-container");
        popup.style.display = "none";
    }
</script>

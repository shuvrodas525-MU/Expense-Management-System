




<!-- JQuery CDN -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Bootstrap js cdn -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>



<script>
    function get_date(){
        let from_date = document.getElementById("from_date").value;
        // let to_date = document.getElementById("to_date").setAttribute("min", from_date);
        let to_date = document.getElementById("to_date");
        to_date.setAttribute("min", from_date);
        
    }

    // jquery delete expense code
    $(document).ready(function(){
        // delete expense code
        $(document).on("click", ".del_expense", function(){
            let result= confirm("Do you really want to delete this expense?");
            if (result){
                let del_id = $(this).attr("id");
               $.ajax({
                url: "delete_expense.php",
                method: "POST",
                data: {del_id: del_id},
                success: function(response){
                    alert("File deleted successfully");
                    window.location.reload();
                }
               })
            }
            
        });
        // delete user code
        $(document).on("click", ".del_user", function(){
            let result = confirm("Do you really want to delete this user?");
            if (result) {
                let del_user_id = $(this).attr("id");
               $.ajax({
                url: "delete_user.php",
                method: "POST",
                data: {del_user_id: del_user_id},
                success: function(response){
                    alert("User deleted successfully");
                    window.location.reload();
                }
               })
            }
            
        });
});
    
</script>
</body>

</html>
$(document).ready(function() {
    $(".avatar-option").on("click", function() {
      const clickedAvatar = $(this);
      $(".avatar-option").removeClass("avatar-selected");
      $(this).addClass("avatar-selected");
      $(this).siblings("input[type=\'radio\']").prop("checked", true);
      const imageUrl = clickedAvatar.attr("src");

      // Enviar el formulario automáticamente al seleccionar un avatar
      Swal.fire({
        title: "¿Deseas cambiar tu foto de perfil?",
        text: "Se actualizará tu foto de perfil inmediatamente.",
        imageUrl: imageUrl,  
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: BASE_URL + "/app/function/usuario//usuario.php",
            type: "POST",
            dataType: "json",
            data: { avatar: $(this).siblings("input[type=\'radio\']").val() 
                  , var_operacion: "cambiar_avatar" },
            success: (response) => {
              Swal.fire({
                icon: "success",
                title: "¡Avatar actualizado!",
                text: response.message,
                timer: 1500,
                showConfirmButton: false
              }).then(() => location.reload());
            },
            error: () => {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo actualizar el avatar. Intenta de nuevo."
              });
            }
          });
        } else {
          // Si se cancela, deseleccionar el avatar
          $(this).removeClass("avatar-selected");
          $(this).siblings("input[type=\'radio\']").prop("checked", false);
        }
      });
      
    });

    $("#actualizar-datos").on("click", function() {
        // obtener los datos del formulario 
        let formData = $("#update_profile_form").serializeArray();
        let name = formData.find(field => field.name === "name").value;
        let username = formData.find(field => field.name === "username").value;
        // Aquí puedes agregar más campos si es necesario  
        $.ajax({
            url: BASE_URL + "/app/function/usuario//usuario.php",
            type: "POST",
            dataType: "json",
            data: { name: name,
                    var_operacion: "actualizar_datos" },
            success: (response) => {
                Swal.fire({
                    icon: "success",
                    title: "¡Datos actualizados!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            },
            error: () => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron actualizar los datos. Intenta de nuevo."
                });
            }
        });
    });
});
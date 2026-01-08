document.addEventListener("DOMContentLoaded", function(event) {
    new TomSelect("#id_proveedor",{
        create: true,
        sortField: {
            field: "text",
            direction: "asc"
        },
        onInitialize: function() {
            this.wrapper.classList.add("tom-select-custom");
        },
        /*onChange: function(value) {
            if(value) {
                this.clear();
            }
        }*/
    });
});

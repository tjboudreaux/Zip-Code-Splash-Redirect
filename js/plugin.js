jQuery(document).ready(function(){
   jQuery(".delete").click(function(){
       var agree = confirm("Are you sure you want to delete this splash page?");
       return agree;
   });
   
   // jQuery("#zip-code-form input").focus(function(){
   //     jQuery(this).val(jQuery(this).val()); 
   // });
});


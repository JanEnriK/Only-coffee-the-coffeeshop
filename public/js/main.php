<style>
    /* Cart Head Styles */
.cart-head {
  position: fixed;
  top: 40%;
  left: 0%;
  background-color: #007bff;
  color: #fff;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  z-index: 9997; 
}

/* Cart Header Styles */
.cart-header {
  background-color: #007bff;
  color: #fff;
  padding: 10px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-header h4 {
  margin: 0;
}

.close-btn {
  border: none;
  background-color: transparent;
  color: #fff;
  cursor: pointer;
  font-size: 20px;
}

.close-btn:hover {
  color: #f00;
}

/* Cart Body Styles */
.cart-body {
  height: 200px;
  overflow-y: auto;
  padding: 10px;
}

/* Cart Footer Styles */
.cart-footer {
  padding: 10px;
  display: flex;
  align-items: center;
}

.cart-footer input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-right: 10px;
}

.cart-footer button {
  padding: 8px 15px;
  background-color: #007bff;
  border: none;
  color: #fff;
  border-radius: 5px;
  cursor: pointer;
}

.cart-footer button:hover {
  background-color: #0056b3;
}

/* Overlay Styles */
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); 
  z-index: 9997; 
  display: none; 
}

/* Cart Styles */
.cart {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 500px;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  display: none; 
  z-index: 9999; 
}


/*chatbot*/
.chat-box {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 500px;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  display: none; 
  z-index: 10000; 
}

.chat-icon {
    position: fixed;
    bottom: 100px;
    right: 15px;
    background-color: #007bff;
    color: #fff;
    width: 80px;
    height: 50px; 
    border-radius: 10px; 
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
  }
  
  .chat-box {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 700px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    display: none; 
    z-index: 9998; 
  }
  
  .chat-header {
    background-color: #007bff;
    color: #fff;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .chat-header h4 {
    margin: 0;
  }
  
  .close-chat-btn {
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
  }
  
  .chat-body {
    padding: 10px;
    height: 200px;
    overflow-y: auto;
  }
  
  .chat-footer {
    display: flex;
    align-items: center;
    padding: 10px;
    margin-top: 40px;
  }
  
  .chat-footer input[type="text"] {
    flex: 1;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  
  .chat-footer button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    margin-left: 10px;
    cursor: pointer;
  }


  .custom-input-group {
    width: 200px; 
}


  .custom-input-field {
      width: calc(100% - 80px); 
  }

  .auto-format {
    display: flex;
    justify-content: space-around;
    align-items: flex-start;
    flex-wrap: wrap;
}
body {
    font-family: Arial, sans-serif;
    background-color: #F5F5DC;
}

table {
    width: 100%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    color: #333;
}

th,
td {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: left;
}

th {
    background-color: #39251e;
    color: #fff;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}



.swal2-container {
    z-index: 9999 !important; 
}
</style>

<script>
    
(function ($) {
    "use strict";
    
    // Dropdown on mouse hover
    $(document).ready(function () {
        function toggleNavbarMethod() {
            if ($(window).width() > 992) {
                $('.navbar .dropdown').on('mouseover', function () {
                    $('.dropdown-toggle', this).trigger('click');
                }).on('mouseout', function () {
                    $('.dropdown-toggle', this).trigger('click').blur();
                });
            } else {
                $('.navbar .dropdown').off('mouseover').off('mouseout');
            }
        }
        toggleNavbarMethod();
        $(window).resize(toggleNavbarMethod);
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });
    

    // Date and time picker
    $('.date').datetimepicker({
        format: 'L'
    });
    $('.time').datetimepicker({
        format: 'LT'
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        margin: 30,
        dots: true,
        loop: true,
        center: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });
    
})(jQuery);

//cart
// Initialize variables and elements
const cartHead = document.getElementById('cartHead');
const cart = document.getElementById('cart');
const overlay = document.getElementById('overlay');
let offsetX = 0;
let offsetY = 0;


function toggleCart() {
    cart.style.display = cart.style.display === 'block' ? 'none' : 'block';
    overlay.style.display = cart.style.display === 'block' ? 'block' : 'none'; // Toggle overlay
    if (cart.style.display === 'block') {
        // Bring cart to front
        cart.style.zIndex = 9999;
        // Hide other content
        document.body.style.overflow = 'hidden';
    } else {
        // Reset z-index
        cart.style.zIndex = '';
        // Show other content
        document.body.style.overflow = 'auto';
    }
}

// Function to handle mouse down event on cart head
function handleMouseDown(event) {
    // Store the initial mouse position relative to the cart head
    offsetX = event.clientX - cartHead.getBoundingClientRect().left;
    offsetY = event.clientY - cartHead.getBoundingClientRect().top;

    // Add event listeners for mouse move and mouse up events
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
}

// Function to handle mouse move event
function handleMouseMove(event) {
    // Calculate new position of the cart head based on mouse position
    const x = event.clientX - offsetX;
    const y = event.clientY - offsetY;

    // Set the position of the cart head
    cartHead.style.left = x + 'px';
    cartHead.style.top = y + 'px';
}

// Function to handle mouse up event
function handleMouseUp() {
    // Remove event listeners for mouse move and mouse up events
    document.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('mouseup', handleMouseUp);
}

// Function to handle closing the cart
function handleCloseCart() {
    cart.style.display = 'none';
    overlay.style.display = 'none'; // Hide overlay
    // Reset z-index
    cart.style.zIndex = '';
    // Show other content
    document.body.style.overflow = 'auto';
}

// Add event listener to close buttons
document.getElementById('closeCart').addEventListener('click', handleCloseCart);
document.querySelector('.close-btn').addEventListener('click', handleCloseCart);

// Add event listener for mouse down event on cart head
cartHead.addEventListener('mousedown', handleMouseDown);

// Add event listener to toggle cart visibility when cart head is clicked
cartHead.addEventListener('click', function() {
    toggleCart();
});




//chatbot form logic
document.addEventListener("DOMContentLoaded", function() {
    function toggleChatbox() {
        const chatBox = document.getElementById("chatBot");
        chatBox.style.display = chatBox.style.display === "none" ? "block" : "none";
        const overlay = document.getElementById("overlay");
        overlay.style.display = chatBox.style.display === "none" ? "none" : "block";
        if (chatBox.style.display === "none") {
            resetForm(); // Reset the form if the chatbox is closed
        }
    }

    function resetForm() {
        form.reset();
        currentStep = 0; // Reset to step 1
        showStep(currentStep);
    }

    function showConfirmation() {
        Swal.fire({
            icon: 'success',
            title: 'Custom coffee order submitted!',
            showConfirmButton: false,
            timer: 1500 
        }).then(() => {
            resetForm(); 
        });
    }

    const toggleChat = document.getElementById("toggleChat");
    toggleChat.addEventListener("click", toggleChatbox);

    const closeChatBtn = document.querySelector(".close-chat-btn");
    closeChatBtn.addEventListener("click", function() {
        toggleChatbox(); // Close the chatbox
    });

});


//increment and decrement buttons 
document.addEventListener("DOMContentLoaded", function() {
    function handleIncrementDecrement(inputId, action) {
        const inputField = document.getElementById(inputId);
        let value = parseInt(inputField.value);
        if (action === "increment") {
            value++;
        } else if (action === "decrement" && value > 0) {
            value--;
        }
        inputField.value = value;
    }

    const incrementButtons = document.querySelectorAll("[id$='-increment']");
    incrementButtons.forEach(button => {
        button.addEventListener("click", function() {
            const inputId = button.id.replace("-increment", "");
            handleIncrementDecrement(inputId, "increment");
        });
    });

    const decrementButtons = document.querySelectorAll("[id$='-decrement']");
    decrementButtons.forEach(button => {
        button.addEventListener("click", function() {
            const inputId = button.id.replace("-decrement", "");
            handleIncrementDecrement(inputId, "decrement");
        });
    });
});

$(document).ready(function() {
    $('.category-btn-checkbox').change(function() {
        if($(this).is(':checked')) {
            const amer = document.getElementById(this.value);
            amer.classList.add("selected");
            $('.category-btn-label').not('#' + this.value).removeClass('selected');
        }
    });
});

var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("chatForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
//   y = x[currentTab].getElementsByTagName("input");
  var y = x[currentTab].querySelectorAll("input[type='radio']:checked");
  // A loop that checks every input field in the current tab:
//   for (i = 0; i < y.length; i++) {
//         var category = document.getElementsByName('category');
//         var CategoryValue = false;
//         for(var i=0; i<category.length;i++){
//             if(category[i].checked == true){
//                 CategoryValue = true;    
//             }
//         }
//         if(!CategoryValue){
//             alert("Please Choose the category");
//             return false;
//         }
//   }
    if (y.length === 0) {
            alert("Please select an option.");
            return false;
        }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}

</script>

//-------------------------------------------------------
function updateSortOption() {
  // Lay gia tri cua "Khoang so tien"
  const amountRange = document.getElementById('amount_range').value;
  // Lay phan tu "Sap xep theo"
  const sortBySelect = document.getElementById('sort_by');
  // Neu khoang so tien duoc chon khac rong, tu dong doi lai thanh "So tien"
  if (amountRange !== '') {
      sortBySelect.value = 'credit';
  }
}

function submitAllForms(){
  // Get all forms on the page
  const forms = document.querySelectorAll('form');
  // Submit each form
  forms.forEach(form => {
      form.submit();
  });
}
//-------------------------------------------------------

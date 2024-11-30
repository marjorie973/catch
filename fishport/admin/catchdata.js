document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('periodSelect');
    const monthYearSelect = document.getElementById('monthYearSelect');
  
    // Get current year
    const currentYear = new Date().getFullYear();
    
    // Function to populate the months (January to December)
    function populateMonths() {
      monthYearSelect.innerHTML = '';  // Clear previous options
      for (let month = 1; month <= 12; month++) {
        const option = document.createElement('option');
        option.value = month;
        option.textContent = new Date(currentYear, month - 1).toLocaleString('default', { month: 'long' });
        monthYearSelect.appendChild(option);
      }
    }
  
    // Function to populate the years (current year and the previous 9 years)
    function populateYears() {
      monthYearSelect.innerHTML = '';  // Clear previous options
      for (let year = currentYear; year >= currentYear - 9; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        monthYearSelect.appendChild(option);
      }
    }
  
    // Enable or disable the second combo box based on the selection in the first combo box
    periodSelect.addEventListener('change', function() {
      const selectedPeriod = periodSelect.value;
  
      if (selectedPeriod === 'monthly') {
        monthYearSelect.disabled = false;
        populateMonths();
      } else if (selectedPeriod === 'yearly') {
        monthYearSelect.disabled = false;
        populateYears();
      } else {
        monthYearSelect.disabled = true;
        monthYearSelect.innerHTML = '';  // Clear options
      }
    });
  
    // Initialize the form (set the default state)
    periodSelect.dispatchEvent(new Event('change'));
  });
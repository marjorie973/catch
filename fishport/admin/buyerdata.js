document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('buyerPeriodSelect');
    const monthYearSelect = document.getElementById('buyerMonthYearSelect');
    const totalIncomeElem = document.getElementById('totalIncome');
    const buyerDataRows = document.querySelectorAll('.buyerRow');

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

    // Function to update the table based on the selected period (Monthly or Yearly)
    function updateTable() {
        const selectedPeriod = periodSelect.value;
        let totalIncome = 0;

        // Show all rows by default
        buyerDataRows.forEach(row => row.style.display = '');

        // Filter rows based on the selected period
        if (selectedPeriod === 'monthly') {
            const selectedMonth = monthYearSelect.value;
            buyerDataRows.forEach(row => {
                const rowMonth = row.getAttribute('data-month');
                const rowYear = row.getAttribute('data-year');
                if (rowMonth !== selectedMonth || rowYear !== currentYear) {
                    row.style.display = 'none';
                }
            });
        } else if (selectedPeriod === 'yearly') {
            const selectedYear = monthYearSelect.value;
            buyerDataRows.forEach(row => {
                const rowYear = row.getAttribute('data-year');
                if (rowYear !== selectedYear) {
                    row.style.display = 'none';
                }
            });
        }

        // Calculate total income
        buyerDataRows.forEach(row => {
            if (row.style.display !== 'none') {
                const incomeCell = row.querySelector('td:nth-child(7)');  // 7th column = Income
                const income = parseFloat(incomeCell.textContent.replace('₱', '').replace(',', ''));
                totalIncome += income;
            }
        });

        // Update the total income row
        totalIncomeElem.textContent = `₱${totalIncome.toLocaleString()}`;
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

        // Update the table based on the new selection
        updateTable();
    });

    // Initialize the form (set the default state)
    periodSelect.dispatchEvent(new Event('change'));
});

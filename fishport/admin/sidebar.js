function sidebarClick(section) {
    // Hide all main sections
    const sections = document.querySelectorAll('main');
    sections.forEach((sec) => {
        sec.style.display = 'none';
    });

    const activeSection = document.getElementById(section);
    if (activeSection) {
        activeSection.style.display = 'block';
    } else {
        console.log('Section not found');
    }

    const sidebarItems = document.querySelectorAll('#sidebar .side-menu li');
    sidebarItems.forEach((item) => {
        item.classList.remove('active');
    });

    const activeItem = [...sidebarItems].find(item => item.querySelector('a').onclick.toString().includes(section));
    if (activeItem) {
        activeItem.classList.add('active');
    }
}
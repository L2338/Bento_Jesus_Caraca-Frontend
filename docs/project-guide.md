# Project Guide for AI Agents - Bento Jesus Caraça Frontend

## 1. Project Overview
- **Name**: Bento Jesus Caraça Frontend
- **Type**: Frontend
- **Description**: Frontend application for the Bento Jesus Caraça website, showcasing his life, works, and legacy
- **Version**: 1.0.0

## 2. Project Structure
```
[Project Root]
├── admin/
│   ├── login.php
│   └── .htaccess
├── assets/
│   ├── css/
│   │   └── main.css        # Includes all CSS including PDF viewer styles
│   ├── img/
│   │   ├── testimonials/
│   │   ├── team/
│   │   ├── tabs/
│   │   ├── logo.png
│   │   ├── hero-bn.jpg
│   │   ├── events-item-2.jpg
│   │   ├── events-item-1.jpg
│   │   ├── epbjc-logo.png
│   │   ├── curso3.png
│   │   ├── curso2.jpg
│   │   ├── curso1.jpg
│   │   ├── course-details.jpg
│   │   ├── about2.jpg
│   │   ├── about-2.jpg
│   │   └── BJC_logo.png
│   ├── js/
│   │   ├── main.js
│   │   └── pdf-viewer.js
│   ├── scss/
│   └── vendor/
│       ├── aos/
│       │   ├── aos.css
│       │   ├── aos.cjs.js
│       │   ├── aos.esm.js
│       │   ├── aos.js
│       │   └── aos.js.map
│       ├── bootstrap/
│       │   ├── css/
│       │   │   ├── bootstrap.css
│       │   │   ├── bootstrap.css.map
│       │   │   ├── bootstrap.min.css
│       │   │   ├── bootstrap.min.css.map
│       │   │   ├── bootstrap.rtl.css
│       │   │   ├── bootstrap.rtl.css.map
│       │   │   ├── bootstrap.rtl.min.css
│       │   │   ├── bootstrap.rtl.min.css.map
│       │   │   ├── bootstrap-grid.css
│       │   │   ├── bootstrap-grid.css.map
│       │   │   ├── bootstrap-grid.min.css
│       │   │   ├── bootstrap-grid.min.css.map
│       │   │   ├── bootstrap-grid.rtl.css
│       │   │   ├── bootstrap-grid.rtl.css.map
│       │   │   ├── bootstrap-grid.rtl.min.css
│       │   │   ├── bootstrap-grid.rtl.min.css.map
│       │   │   ├── bootstrap-reboot.css
│       │   │   ├── bootstrap-reboot.css.map
│       │   │   ├── bootstrap-reboot.min.css
│       │   │   ├── bootstrap-reboot.min.css.map
│       │   │   ├── bootstrap-reboot.rtl.css
│       │   │   ├── bootstrap-reboot.rtl.css.map
│       │   │   ├── bootstrap-reboot.rtl.min.css
│       │   │   ├── bootstrap-reboot.rtl.min.css.map
│       │   │   ├── bootstrap-utilities.css
│       │   │   ├── bootstrap-utilities.css.map
│       │   │   ├── bootstrap-utilities.min.css
│       │   │   ├── bootstrap-utilities.min.css.map
│       │   │   ├── bootstrap-utilities.rtl.css
│       │   │   ├── bootstrap-utilities.rtl.css.map
│       │   │   ├── bootstrap-utilities.rtl.min.css
│       │   │   ├── bootstrap-utilities.rtl.min.css.map
│       │   │   └── prb.txt
│       │   └── js/
│       │       ├── bootstrap.bundle.js
│       │       ├── bootstrap.bundle.js.map
│       │       ├── bootstrap.bundle.min.js
│       │       ├── bootstrap.bundle.min.js.map
│       │       ├── bootstrap.esm.js
│       │       ├── bootstrap.esm.js.map
│       │       ├── bootstrap.esm.min.js
│       │       ├── bootstrap.esm.min.js.map
│       │       ├── bootstrap.js
│       │       ├── bootstrap.js.map
│       │       ├── bootstrap.min.js
│       │       └── bootstrap.min.js.map
│       ├── bootstrap-icons/
│       ├── glightbox/
│       │   ├── css/
│       │   └── js/
│       ├── php-email-form/
│       ├── purecounter/
│       └── swiper/
│           ├── swiper-bundle.min.css
│           ├── swiper-bundle.min.js
│           └── swiper-bundle.min.js.map
├── docs/
│   ├── arquitetura-vibe-coding.md
│   ├── regras-vibe-coding.md
│   └── project-guide.md
├── includes/
│   └── components/
│       ├── footer.php
│       └── Menu.php
├── pages/
│   ├── index.php
│   ├── start-page.php
│   ├── vida.php
│   ├── obras.php
│   ├── legado.php
│   └── course-details.php
├── storage/
│   └── books/
│       └── pdfs/
│           └── Obras/
│               └── Obra3.pdf
├── .gitignore
├── package.json
└── README.md
```

## 3. Technology Stack
- **Frontend**: PHP, HTML5, CSS3, JavaScript
- **Build Tools**: None (Direct PHP interpretation)
- **Package Manager**: npm (for frontend dependencies)
- **Server**: Apache (XAMPP)

## 4. Coding Standards and Guidelines
- **Language**: PHP 7+, JavaScript (ES6+)
- **Style Guide**: PSR-12 for PHP
- **File Naming**: 
  - Lowercase with hyphens for pages (e.g., `start-page.php`)
  - Lowercase for components and includes
  - Camelcase for JavaScript files
- **Component Structure**: Modular PHP includes

## 5. Project Rules and Constraints
1. All assets must be organized in appropriate folders (css, js, img)
2. PDF files must be stored in storage/books/pdfs
3. Admin area must be protected with .htaccess
4. Follow responsive design principles
5. Maintain clear separation between public and admin areas
6. Use semantic HTML5 elements
7. Implement proper error handling
8. Follow accessibility (a11y) best practices
9. Keep code DRY (Don't Repeat Yourself)
10. Implement proper security measures

## 6. Development Workflow
1. Clone the repository
2. Set up XAMPP environment
3. Configure Apache virtual host
4. Start development server
5. Access via localhost

## 7. Security Measures
- Implement proper authentication for admin area
- Use .htaccess for directory protection
- Sanitize user inputs
- Implement CSRF protection
- Use secure file upload handling
- Protect sensitive directories
- Implement proper session management

## 8. File Organization
### Assets
- CSS files in assets/css (main.css contains all styles including PDF viewer)
- JavaScript files in assets/js
- Images in assets/img
- Vendor files in assets/vendor

### Storage
- All PDF files centralized in storage/books/pdfs/Obras
- Protected via .htaccess

### Components
- Reusable components in includes/components
- Header, footer, and navigation components

### Admin Area
- Protected admin interface
- Login system
- Book management functionality

## 9. Documentation
- Project architecture in docs/arquitetura-vibe-coding.md
- Coding rules in docs/regras-vibe-coding.md
- Project guide in docs/project-guide.md

## 10. Version Control
- Use Git for version control
- Follow meaningful commit messages
- Keep documentation updated
- Regular backups of important files

## 11. Performance Optimization
- Optimize images before upload
- Minify CSS and JavaScript
- Implement proper caching
- Optimize PDF loading
- Use lazy loading where appropriate

## 12. Accessibility
- Use semantic HTML elements
- Implement proper ARIA attributes
- Ensure keyboard navigation
- Maintain proper color contrast
- Provide alt text for images

## 13. Error Handling
- Implement proper PHP error handling
- Log important errors
- Display user-friendly error messages
- Handle file upload errors gracefully
- Implement form validation

## 14. Maintenance
- Regular security updates
- Backup of important files
- Code optimization when needed
- Documentation updates
- Regular testing of all features

## 15. AI Agent Instructions
- Always check existing file structure before modifications
- Maintain security measures, especially in admin area
- Keep PDF organization consistent
- Follow established naming conventions
- Update documentation when making changes
- Ensure accessibility compliance
- Test thoroughly before implementing changes

## 16. Page Structure
- **Home (index.php)**: Main landing page
- **Start Page**: Introduction to the website
- **Vida**: Biography of Bento Jesus Caraça
- **Obras**: Collection of works and publications
- **Legado**: Legacy and impact
- **Course Details**: Specific course information

## 17. Admin Features
- Secure login system
- PDF management
- Content management
- User management
- File upload handling

Remember to maintain the established structure and follow security best practices when making any modifications to the project. 
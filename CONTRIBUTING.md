# Contributing to CareerForge

Thank you for considering contributing to **CareerForge - Job Portal**! Contributions from the community help make this project better for everyone.

---

## Code of Conduct

Please be respectful and considerate of other contributors. Review and adhere to our [Code of Conduct](CODE_OF_CONDUCT.md).

---

## How to Contribute

### 1. Reporting Bugs
- Check the [existing issues](https://github.com/ajayrathod-dev/Careerforge-A-Job-Portal/issues) before opening a new one.
- Provide a clear, descriptive title.
- Describe the bug with detailed steps to reproduce it.
- Include your environment details (PHP version, MySQL/MariaDB version, Apache/XAMPP version, Browser).

### 2. Suggesting Features
- Open an issue tagged as `enhancement`.
- Describe the feature, why it is beneficial, and how it could be implemented.

### 3. Submitting Pull Requests (PRs)
1. **Fork the repository** on GitHub.
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/your-username/Careerforge-A-Job-Portal.git
   cd Careerforge-A-Job-Portal
   ```
3. **Create a descriptive feature branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **Make your changes**:
   - Write clean, commented PHP and JavaScript.
   - Use prepared statements (PDO / MySQLi) to prevent SQL injections.
   - Follow standard HTML5 semantic elements and responsive styling.
5. **Test your changes** thoroughly on local XAMPP/WAMP environment.
6. **Commit your changes** with clear, concise messages:
   ```bash
   git commit -m "feat: Add job bookmarking capability"
   ```
7. **Push to your branch**:
   ```bash
   git push origin feature/your-feature-name
   ```
8. **Open a Pull Request** against the `main` branch with a summary of changes.

---

## Coding Standards

- **PHP**: PSR-12 coding standard guidelines. Always sanitize user inputs and utilize parameterized queries for database operations.
- **Frontend**: Bootstrap 5 responsive grid conventions, clean semantic HTML5, and modular CSS.
- **Naming Conventions**: Use `snake_case` for PHP variables and file names, and `camelCase` for JavaScript functions and variables.

---

Thank you for making CareerForge better!

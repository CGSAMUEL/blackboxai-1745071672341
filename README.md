
Built by https://www.blackbox.ai

---

```markdown
# Marvel Info

## Project Overview
Marvel Info is a web application that allows users to explore the vast universe of Marvel comics. Users can access the application anonymously or create an account to unlock additional features. The app integrates various functionalities like user management, character and comic searches, and much more.

## Installation
To set up the project locally, follow these steps:

1. Clone the repository:
   ```bash
   git clone <repository_url>
   cd <repository_directory>
   ```

2. Set up your web server (e.g., XAMPP, WAMP, or any other PHP server). Place the cloned repository in the server's document root (e.g., `htdocs` for XAMPP).

3. Configure the database:
   - Create a MySQL database named `db_grupo08`.
   - Ensure you have the necessary tables (`users`, `final_comics`, `final_characters`, etc.) in your database. You may need to import additional SQL files if provided with the project.

4. Update database connection details in `conexionDB.php` with your own database credentials.

5. Start your web server and access the application via your browser at:
   ```
   http://localhost/index.php
   ```

## Usage
- **Home Page:** Visitors can continue as a guest or click on "Register" or "Login" to create an account or access their existing one.
- **Dashboard:** After logging in, users can search for Marvel characters, comics, and creators, and see user-specific content.
- **Registration & Login:** Users can register with a username, email, and password. Existing users can log in with their credentials.

## Features
- User registration and login functionality.
- Anonymous guest access.
- Searching capabilities for Marvel characters, comics, and creators.
- User-specific content displayed on the main page after logging in.
- Responsive design optimized for different screen sizes.

## Dependencies
The project utilizes the following dependencies:
- **Frontend:** 
  - Tailwind CSS for styling (via CDN)
  - Font Awesome for icons (via CDN)
- **Backend:** 
  - PHP and MySQL for server-side logic and database management.
  
Please ensure your PHP server is properly configured to support these dependencies.

## Project Structure
Here's a brief overview of the project's structure:

```
/<project-root>
│
├── index.php             # Landing page with guest and user options
├── main.php              # Main user page after login
├── registration.php       # User registration page
├── login.php              # User login page
├── dashboard.php          # User dashboard for searching and viewing content
├── logout.php             # Script to logout the user
├── listarUsuarios.php     # Script to list users (admin function)
├── buscarComics.php       # Script to search for comics
├── buscarCreadores.php     # Script to search for creators
├── buscarPersonajes.php    # Script to search for characters
├── conexionDB.php         # Database connection script
│
└── <other_required_files> # Other necessary files
```

## Contributing
Contributions are welcome! Please fork the repository and create a pull request with your changes.

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements
- Special thanks to the creators of the underlying technologies used in this project.
```

Ensure to replace `<repository_url>` and `<repository_directory>` with the actual values specific to your setup before sharing or using the README.md.
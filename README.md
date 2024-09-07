# Interactive Fiction Framework

## Project Aim

The goal of this project is to create a robust, scalable framework for interactive fiction using only **HTML**, **CSS**, **PHP**, and **JavaScript** (if necessary). This framework will allow for the creation and display of interactive stories that record player choices for data analysis.

Unlike typical full-stack development approaches, this project aims to execute everything on a simpler stack by leveraging PHP for server-side processing and file-based storage (or databases if extended later).

## Features

- **Interactive Storytelling**: Players can make choices that affect the outcome of the narrative, with stories presented using HTML and styled with CSS.
- **Choice Tracking**: Player choices will be recorded for later analysis, initially stored in text files.
- **Modular Design**: Using PHP, reusable components such as headers and footers are included across multiple pages for ease of development.
- **Analytics**: Basic tracking of player decisions through PHP, with possible extensions into JavaScript for more advanced user interaction tracking.

## Technologies Used

- **HTML**: Structure and presentation of content.
- **CSS**: Styling and layout of interactive stories.
- **PHP**: Backend processing, including handling player choices and analytics.
- **JavaScript** (optional): May be used for enhanced interactivity, but will be minimized in favor of server-side processing.

## Directory Structure
```plaintext
├── assets
│   ├── css/               # Stylesheets
│   ├── images/            # Image assets
│   └── js/                # Optional JavaScript files
│
├── pages
│   ├── index.php          # Main page
│   ├── story.php          # Interactive story logic
│   └── analytics.php      # Handles data collection and analysis
│
├── templates
│   ├── header.php         # Reusable header
│   └── footer.php         # Reusable footer
│
├── data
│   └── choices.txt        # File to record player choices
│
├── documentation
│   ├── design-docs.md     # Initial design docs and decisions
│   ├── lit-review.md      # Literature review an analysis
│   └── code-reviews.md    # Writeups on code architecture and reviews
│
└── README.md              # Project overview
```
## Future Extensions

While the initial implementation uses file-based storage, the framework can later be extended to support more complex storage mechanisms such as relational databases, providing greater flexibility and scalability.

## How to Run (TBI)

1. Clone the repository or download the files.
2. Ensure PHP is installed on your server.
3. Place the project in your server's root directory.
4. Open `index.php` in your browser to start the interactive experience.

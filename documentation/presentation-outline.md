# PASSAGES: Creating and Exploring Interactive Fiction
*Christopher Magus Øvringmo Nilssen* —  Creator & Presenter
*Sarah Carruthers* — Faculty Mentor
*CSCI 490 FALL 2024*

---

## Presentation Outline

**Total Duration:** 30 minutes

---

### 1. Introduction (5 minutes)

- **Personal Background**
  - Brief introduction:
    - **35+ years gaming experience**
    - **Graduated from Vancouver Film School** with diplomas in Foundation Art and Design, and Game Design
    - **Computer science background:** Studied for a Computing Science Bachelor's degree from Sept 2016-Dec 2019 (actually failed Web Programming!), returned this Sept after five years of a Creative Writing Bachelor's to complete a Minor in Computer Science.
    - **5 years as an independent video game developer and author**
      - **40 published games**
      - **3 published science fiction books**
  - **Passion for storytelling and interactive media**
- **Motivation for the Project**
  - Combining my love for gaming and storytelling
  - To gain a deeper understanding of interactive fiction and full-stack development.
  - Desire to empower others to tell their stories through interactive media

---

### 2. Project Overview (3 minutes)

- **What is Interactive Fiction?**
  - Definition and brief history
  - Examples of popular interactive fiction works
- **Purpose and Goals of PASSAGES**
  - Provide a platform for both creators and players of interactive fiction
  - Emphasize ease of use and accessibility
  - Foster a community around storytelling and creativity

---

### 3. Demonstration of Key Features (10 minutes)

- **User Authentication and Session Management**
  - Secure login system
  - Session handling to protect user data
- **Story Creation and Management**
  - Creating new stories
  - Editing and deleting existing stories
  - Organizing stories for easy access
- **Passage and Choice Management**
  - Adding and editing passages
  - Linking passages through choices
  - Navigating between passages
  - Viewing incoming and outgoing choices
- **Interactive User Interface**
  - Dynamic forms using JavaScript
  - Toggle between linking to existing passages or creating new ones
  - Success and error messaging for user actions
- **Playing Stories**
  - Landing page showcasing available stories
  - Story playback functionality with progress tracking
  - User experience for both logged-in and guest users

---

### 4. Technical Implementation (7 minutes)

- **Backend Architecture**
  - **Database Design**
    - Explanation of the relational database schema
    - Tables: Users, Stories, Passages, Choices, Tags, StoriesPlayed
    - How data relationships facilitate story progression and user tracking
  - **Security Measures**
    - Input validation and sanitization
    - CSRF protection
    - Prepared statements to prevent SQL injection
  - **Code Organization**
    - Separation of concerns: backend logic, frontend presentation, JavaScript functionalities
    - Use of functions and modular code for maintainability
- **Frontend Design**
  - Basic HTML interface focused on usability
  - Consistent layout and navigation for a seamless experience
  - Accessibility considerations (e.g., play without login)

---

### 5. Challenges and Solutions (3 minutes)

- **Technical Challenges**
  - Managing database relationships for complex story structures
  - Implementing secure user authentication
  - Ensuring a user-friendly interface with dynamic content
  - Remembering how to do anything game programming related!
- **Solutions**
  - Utilizing transactions and prepared statements in SQL
  - Adopting best practices for web security
  - Iterative user interface testing and refinement

---

### 6. Future Enhancements (2 minutes)

- **Features Under Consideration**
  - **AJAX Data Loading** for improved performance with large stories
  - **Further Code Refactoring** for scalability
  - **Improved UI/UX** enhancements based on user feedback
  - **Additional Security Measures** like robust authentication and authorization checks
  - **Move Toward Genuine Interactive Fiction** by developing more co-authorship functionality between player and creator
- **Not Yet Implemented**
  - Visualization of story structures
  - Variables and state management within stories
  - Markdown parsing for passage text formatting
  - Player data tracking and analysis

---

### 7. Conclusion (2 minutes)

- **Summary of Achievements**
  - Developed a functional prototype within the project timeline
  - Successfully integrated core features for creating and playing hypertext fiction
  - Established a foundation for future development and enhancements
  - Gained a thorough appreciation for the gap between hypertext and interactive fiction
- **Potential Impact**
  - Empowering creators to share their stories interactively
  - Building a community around interactive fiction
  - Contributing to the field of digital storytelling and game development
  - Scaffolding the infrastructure necessary for future more robust interactive fiction architecture

---

### 8. Q&A Session (Up to 5 minutes)

- Open the floor for questions from the audience
- Encourage discussion on technical aspects, user experience, and future possibilities

---


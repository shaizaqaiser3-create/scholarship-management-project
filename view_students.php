<div style="display: flex; gap: 10px; flex-wrap: wrap;">
    <a href="student_profile.php?id=<?php echo $student['user_id']; ?>" 
       class="action-btn profile-btn">👤 View Profile</a>
    <a href="student_education.php?id=<?php echo $student['user_id']; ?>" 
       class="action-btn education-btn">📚 Education</a>
    <a href="mailto:<?php echo $student['email']; ?>" 
       class="action-btn email-btn">📧 Email</a>
</div>
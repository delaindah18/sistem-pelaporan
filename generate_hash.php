<?php
echo "Admin (admin123): " . password_hash('admin123', PASSWORD_DEFAULT) . "<br><br>";
echo "Petugas (petugas123): " . password_hash('petugas123', PASSWORD_DEFAULT);
?>
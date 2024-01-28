<?php require "view_begin.php"; ?>
<?php require "view_header_connected.php"?>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Lien GitHub</th>
            <th>Date du GitHub</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($liens as $lien): ?>
        <tr>
            <td><?=e($lien['nom_utilisateur'])?></td>
            <td><?=e($lien['prenom_utilisateur'])?></td>
            <td><?php echo '<a href="'. e($lien['lien']) .'">';?><?=e($lien['lien'])?></a></td>
            <td><?=e($lien['date_du_github'])?></td>
            <td>
                <a href="?controller=paneladmin&action=accepter&id=<?=e($lien['id_github'])?>">Accepter</a></td>
            <td>
                <a href="?controller=paneladmin&action=refuser&id=<?=e($lien['id_github'])?>">Refuser</a>
            </td>
        </tr>
     <?php endforeach; ?>
    </tbody>
</table>

<?php require "view_end.php"; ?>
<?php require "view_begin.php"; ?>
<?php require "view_header_connected.php"?>
<table>
    <thead>
        <tr>
            <th>Nom,</th>
            <th>Prénom,</th>
            <th>Lien GitHub,</th>
            <th>Nom de Dossier,</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($liens as $lien): ?>
        <tr>
            <td><?=e($lien['nom_utilisateur'])?></td>
            <td><?=e($lien['prenom_utilisateur'])?></td>
            <td><?php echo '<a href="'. e($lien['lien_github']) .'">';?><?=e($lien['lien_github'])?></a></td>
            <td><?=e($lien['nom_dossier'])?></td>
            <td>
                <a href="?controller=paneladmin&action=supprimer&id=<?=e($lien['id_site'])?>">Refuser</a>
            </td>
        </tr>
     <?php endforeach; ?>
    </tbody>
</table>

<?php require "view_end.php"; ?>
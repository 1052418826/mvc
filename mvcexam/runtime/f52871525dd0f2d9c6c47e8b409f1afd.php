<?php echo $data;?>

<form action="index" method='post'>
<table>
    <tr>
        <td>许愿人</td>
        <td><input type="text" name='name'></td>
    </tr>
    <tr>
        <td>许愿内容</td>
        <td><textarea name="content" cols="30" rows="10"></textarea></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" value='提交'></td>
    </tr>
</table>
</form>
编译 CHM 的步骤：
    1. 按正常编译产生 HTML 文件、htmlhelp.hhp 和 toc.hhc 文件。
    2. php convEncoding.php toc.hhc
    3. php convEncoding.php htmlhelp.hhp
    4. 修改 toc.hhc 编码为 GB18030。并确保文件本身使用 ANSI 编码保存。
    5. 其他文件采用 UTF-8 保存。
    6. 使用 HTML Help Workshop 或其他可用于编译 CHM 文件的程序生成 CHM。
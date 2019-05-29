# pissalo-tool
tool
用户手册

    Arr::class
        \Yiranzai\Tools\Arr::sortBy() // 使用给定的回调对数组进行排序并保留原始键，支持多列排序
        \Yiranzai\Tools\Arr::arrSortByField() // 二维数组排序
        \Yiranzai\Tools\Arr::arrGroup() // 数组按字段分组
        \Yiranzai\Tools\Arr::heapSort() // 堆排序
        \Yiranzai\Tools\Arr::mergeSort() // 归并排序
        \Yiranzai\Tools\Arr::quickSort() // 快速排序
    Date::class
        \Yiranzai\Tools\Date::toCarbon() // 生成 Carbon 对象
        \Yiranzai\Tools\Date::timeDiffFormat() // 输出两个 DateTime 对象的差距
    Math::class
        \Yiranzai\Tools\Math::formatDiv() // 四舍五入 格式化除法
        \Yiranzai\Tools\Math::formatMod() // 四舍五入 格式化取余（模运算）
        \Yiranzai\Tools\Math::formatMul() // 四舍五入 格式化乘法
        \Yiranzai\Tools\Math::formatSub() // 四舍五入 格式化减法
        \Yiranzai\Tools\Math::formatAdd() // 四舍五入 格式化加法
        \Yiranzai\Tools\Math::gcd() // 求两个数的最大公约数
        \Yiranzai\Tools\Math::gcdArray() // 求一个数组的最大公约数
    Filesystem::class
        \Yiranzai\Tools\Filesystem::hash() // 获取给定路径上的文件的 MD5 哈希值。
        \Yiranzai\Tools\Filesystem::prepend() // 将内容存储到到文件开头。
        \Yiranzai\Tools\Filesystem::exists() // 确定文件或目录是否存在。
        \Yiranzai\Tools\Filesystem::put() // 将内容存储在文件中。
        \Yiranzai\Tools\Filesystem::makeDirectory() // 创建一个目录。
        \Yiranzai\Tools\Filesystem::get() // 获取文件的内容。
        \Yiranzai\Tools\Filesystem::isFile() // 确定给定路径是否为文件。
        \Yiranzai\Tools\Filesystem::sharedGet() // 获取具有共享访问权限的文件的内容。
        \Yiranzai\Tools\Filesystem::size() // 获取给定文件的文件大小。
        \Yiranzai\Tools\Filesystem::append() // 将内容存储到到文件结尾（追加）。
        \Yiranzai\Tools\Filesystem::chmodFile() // 获取或设置文件或目录的 UNIX 模式。
        \Yiranzai\Tools\Filesystem::move() // 将文件移动到新位置。
        \Yiranzai\Tools\Filesystem::name() // 从文件路径中提取文件名。
        \Yiranzai\Tools\Filesystem::basename() // 从文件路径中提取尾随名称组件。
        \Yiranzai\Tools\Filesystem::dirname() // 从文件路径中提取父目录。
        \Yiranzai\Tools\Filesystem::extension() // 从文件路径中提取文件扩展名。
        \Yiranzai\Tools\Filesystem::type() // 获取给定文件的文件类型。
        \Yiranzai\Tools\Filesystem::mimeType() // 获取给定文件的 mime 类型。
        \Yiranzai\Tools\Filesystem::lastModified() // 获取文件的上次修改时间。
        \Yiranzai\Tools\Filesystem::isReadable() // 确定给定路径是否可读。
        \Yiranzai\Tools\Filesystem::isWritable() // 确定给定路径是否可写。
        \Yiranzai\Tools\Filesystem::moveDirectory() // 移动目录。
        \Yiranzai\Tools\Filesystem::isDirectory() // 确定给定路径是否是目录。
        \Yiranzai\Tools\Filesystem::deleteDirectory() // 递归删除目录。
        \Yiranzai\Tools\Filesystem::delete() // 删除给定路径的文件。
        \Yiranzai\Tools\Filesystem::copyDirectory() // 将目录从一个位置复制到另一个位置。
        \Yiranzai\Tools\Filesystem::copyFile() // 将文件复制到新位置。
        \Yiranzai\Tools\Filesystem::cleanDirectory() // 清空所有文件和文件夹的指定目录。
        \Yiranzai\Tools\Filesystem::windowsOs() // 确定当前环境是否基于 Windows。
    Tools::class
        \Yiranzai\Tools\Tools::getNiceFileSize() // 人性化转化内存信息
        \Yiranzai\Tools\Tools::callFunc() // 调用对象的方法
        \Yiranzai\Tools\Tools::iteratorGet() // 获取一个对象或者一个数组的属性
        \Yiranzai\Tools\Tools::arrGet() // 获取数组中的某个元素
        \Yiranzai\Tools\Tools::objectGet() // 获取对象中的某个元素
    SnowFlake::class
        \Yiranzai\Snowflake\SnowFlake::next() // 生成 64 位 id
        \Yiranzai\Snowflake\SnowFlake::analysis() // 解析 64 位 id


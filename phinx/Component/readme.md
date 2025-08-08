### 修改记录

#### 帮助功能修改

```shell
# 默认为txt
seed:run --help --format=txt

# 删除以下内容只保留txt
seed:run --help --format=json
seed:run --help --format=xml
seed:run --help --format=md
```

删除Descriptor目录下的

    MarkdownDescriptor.php
    JsonDescriptor.php
    XmlDescriptor.php

修改Helper目录下的DescriptorHelper.php

#### 内置命令

只保留HelpCommand,ListCommand,删除complete(自动补全)方法

删除:

    CompleteCommand
    DumpCompletionCommand
    LazyCommand




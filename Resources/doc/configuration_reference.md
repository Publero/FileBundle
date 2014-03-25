Configuring Publero FileBundle
==============================

In order to get *FileBundle* working you need to configure *graufrette adapter* and
*filesystem* and then set it in FileBundle`s configuration.

You can set default *filesystem* with `filesystem` parameter.

## Example gaufrette configuration

``` yaml
knp_gaufrette:
    adapters:
        data:
            local:
                directory: %kernel.root_dir%/data
                create: true
    filesystems:
        data:
            adapter: data
            alias: data
```

## Example FileBundle configuration

``` yaml
publero_file:
    filesystem: gaufrette.data_filesystem
```

# Purple CLI - Key

Purple is using two keys, Secret and Production key. Secret key is a key for reading the encrypted database information in the config. It is used for decrypting the database information. Production key is a key for moving your data to the new server.


To generate both keys, use command below

```bash
bin/cake purple key generate
```

A secret key will be written in <code>config/secret.key</code>, and production key will be written in <code>config/production_key.php</code>.
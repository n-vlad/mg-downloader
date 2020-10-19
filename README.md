# mg-downloader

## Installation

The docker files needed to build the basic image are available within the project.

Run the following command from the **project root** to build the image:
```
docker build -t mg-downloader docker/development/
```

Construct the container from the **project root** with the following command:
```
docker-compose up -d
```

## Usage

The database will be automatically setup and ready to use, there are no configurations or extra commands required.

To trigger the download process, run the following command from within the container:
```
./bin/console mg:download
```

Once the download is complete, head to the interface on port **8001** and use the credentials ```admin/admin```

Stored groups can be viewed under: ```Media Library > Gallery```

Stored individual images can be viewed under: ```Media Library > Media```

Unit tests can be triggered from the container with the following command:
```
./bin/console phpunit
```

Endpoint can be modified via the environment variable ```APP_ENDPOINT```
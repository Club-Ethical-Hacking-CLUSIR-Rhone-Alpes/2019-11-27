package main

import (
	"net/http"
	"log"
	"io/ioutil"
	"fmt"
	"os/user"
	"os"
)

func main() {
	fmt.Println(track("127.0.0.1:6007", "edit-my-face"))
}

func httpGet(url string) string {
	client := &http.Client{}

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
			log.Fatalln("{\"processed\": false}")
	}

	req.Header.Set("User-Agent", "track-fantastic/vortex-2.4")

	resp, err := client.Do(req)
	if err != nil {
		log.Fatalln("{\"processed\": false}")
	}

	defer resp.Body.Close()
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Fatalln("{\"processed\": false}")
	}
	return string(body)
}

func track(endpoint string, software string) string {
	source := "track-fantastic-go"
	user, err := user.Current()
	hostname, err := os.Hostname()
	if err != nil {
		log.Fatalln("{\"processed\": false}")
		return "{\"processed\": false}"
	}
	response := httpGet("http://" + endpoint + "/client/vortex/" + source + "/" + hostname + "/windows/{\"software\":\""+software+"\",\"user\":\"" + user.Username + "\"}")

    return response
}
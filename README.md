# sms-to-csv-converter
Converts multiple VMG files to CSV

This is a command line PHP application, created to convert multiple sms files (.VMG) into a single CSV file. This application reads all the .VMG files in given folder and creates a CSV file with the following columns.

 * Date
 * Message type (INBOX/SENTBOX)
 * Mobile Number
 * Text Message

## Requirement
You need to have installed PHP to be available globally.
Type `php -v` in terminal to check you have it.

## Preparation

Kindly prepare a folder that contains all your .VMG files.
Download this Repository into your computer.

## Usage

Open your command prompt.
Navigate to the downloaded repository folder.
Type the following command.

`php sms.php path/to/your/vmg/folder output_file_name`

Hit enter.
You will see the exported CSV file in the repository directory. [OR]
You will see an error message if you entered an invalid directory.

Note: `output_file_name` is optional. The output file is always .csv and you dont need to give the file extenion. If you dont give the output_file_name, file will be generated with the name: `SMS_output_YYYY_MM_DD HH_MM_SS.csv`

This application will process only the .vmg files in the given directory. Other files in the given directory will be automatically ignored.

### Contribute

Pull requests are welcome.

In lieu of a formal style guide, take care to maintain the existing coding style.

### License

Copyright (c) 2017 Senthilprabu Ponnusamy

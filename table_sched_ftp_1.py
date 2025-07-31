import schedule
import os
import time

def job():
    os.system("D:\\xampp\\htdocs\\breaklunch\\update_ftp_nextview_1.bat")
    print("Success!  update_ftp_nextview_1.bat")

schedule.every().day.at("05:01").do(job)

while True:
    schedule.run_pending()
    time.sleep(1)

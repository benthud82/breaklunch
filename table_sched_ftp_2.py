import schedule
import os
import time

def job():
    os.system("D:\\xampp\\htdocs\\breaklunch\\update_ftp_nextview_2.bat")
    print("Success!  update_ftp_nextview_2.bat")

schedule.every().day.at("23:00").do(job)

while True:
    schedule.run_pending()
    time.sleep(1)

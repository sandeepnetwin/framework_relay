from PyQt4.QtGui import *
from PyQt4.QtCore import *
import sys
import foscam
import Image
from StringIO import StringIO
import time
import cv2

ImageReadyEventId = 1382

faceCascade = cv2.CascadeClassifier('haarcascade_frontalface_alt2.xml')


class ImageReadyEvent(QEvent):
  def __init__(self, image):
    QEvent.__init__(self, ImageReadyEventId)
    self._image = image

    def image(self):
      return self._image

class IPCam():
  def __init__(self):
    self.foscam = foscam.FoscamCamera('70.189.244.70:8099', 'admin', 'lucky777')

  def up(self):
    self.direction = self.foscam.UP
    self.foscam.move(self.direction)

  def down(self):
    self.direction = self.foscam.DOWN
    self.foscam.move(self.direction)

  def left(self):
    self.direction = self.foscam.LEFT
    self.foscam.move(self.direction)

  def right(self):
    self.direction = self.foscam.RIGHT
    self.foscam.move(self.direction)

  def stop(self):
    self.foscam.move(self.direction + 1)

  def playVideo(self):
    self.foscam.startVideo(videoCallback, self)

  def stopVideo(self):
    self.foscam.stopVideo()

  def event(self, e):
    if e.type() == ImageReadyEventId:
      data = e.image()
      im = Image.open(StringIO(data))
      self.qim = QImage(im.tostring(), im.size[0], im.size[1], QImage.Format_RGB888)
      self.pm = QPixmap.fromImage(self.qim)
      #self.image_label.setPixmap(self.pm)
      #self.image_label.update()
      with open('picture.png', 'wb') as f:
        f.write(self.pm)
      return 1
    return QWidget.event(self, e)

import numpy as np
def videoCallback(frame, userdata=None):
  nparr = np.fromstring(frame, np.uint8)
  img = cv2.imdecode(nparr, cv2.CV_LOAD_IMAGE_COLOR)
  gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
  faces = faceCascade.detectMultiScale(
    gray,
    scaleFactor=1.1,
    minNeighbors=5,
    minSize=(30,30),
    flags = cv2.cv.CV_HAAR_SCALE_IMAGE
  )
  for (x, y, w, h) in faces:
    cv2.rectangle(img, (x, y), (x + w, y + h), (0, 255, 0), 2)
    print 'Found face'
  #with open('picture.png', 'wb') as f:
  #  f.write(img)
  cv2.imwrite('picture.png', img)

if __name__ == '__main__':
  cam = IPCam()
  cam.playVideo()
  time.sleep(1)
  cam.stopVideo()
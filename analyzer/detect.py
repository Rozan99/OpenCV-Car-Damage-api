import cv2
import numpy as np
import sys
import json
import os

def detect_damage(image_path):
    # image_path is absolute, no path modification needed
    if not os.path.exists(image_path):
        print(json.dumps({
            "error": "Image file not found.",
            "score": None,
            "level": "error"
        }))
        return

    img = cv2.imread(image_path)
    if img is None:
        print(json.dumps({
            "error": "Failed to load image.",
            "score": None,
            "level": "error"
        }))
        return

    try:
        img = cv2.resize(img, (640, 480))
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        blurred = cv2.GaussianBlur(gray, (5, 5), 0)
        equalized = cv2.equalizeHist(blurred)

        edges = cv2.Canny(equalized, 50, 120)
        hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
        mask = cv2.inRange(hsv, (0, 0, 0), (180, 50, 255))
        combined = cv2.bitwise_or(edges, mask)

        contours, _ = cv2.findContours(combined, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

        damage_count = 0
        for cnt in contours:
            area = cv2.contourArea(cnt)
            if area > 300:
                x, y, w, h = cv2.boundingRect(cnt)
                aspect_ratio = w / h
                if 0.2 < aspect_ratio < 5.0:
                    damage_count += 1

        damage_score = damage_count * 10
        if damage_score < 30:
            label = "Minor"
        elif damage_score < 60:
            label = "Moderate"
        else:
            label = "Severe"

        result = {
            "score": damage_score,
            "level": label
        }

        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({
            "error": str(e),
            "score": None,
            "level": "error"
        }))

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({
            "error": "Invalid number of arguments",
            "score": None,
            "level": "error"
        }))
    else:
        detect_damage(sys.argv[1])

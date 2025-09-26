import os
import json
import re

with open("text2.txt", "r") as f:
    text = f.read()

clean_str = re.sub(r"^```json\s*|\s*```$", "", text, flags=re.DOTALL)
json_data = json.loads(clean_str)
for key, value in json_data.items():
    print(f"{key}: {value}",end="\n\n")
from django.shortcuts import render
from django.http import  HttpResponse
from .models import Lugat






#from django.http import  HttpResponse
#from django. ......  import .......



def index(request):
# 	context = {
#     'ad_size': '300x250',
#     'ad_targeting': 'sports'
#   }
   soz = request.GET.get('q', '')
   if soz and soz != '':
       natija = Lugat.objects.filter(ruscha__contains=soz).all()[:3]
#       natija = Lugat.objects.filter(inglizcha__contains=soz).all()[:3]
       natija = Lugat.objects.filter(uzbekcha__contains=soz).all()[:3]

#       for data in natija:
#           print(data.inglizcha)

   else:
   	   natija = None

   return render(request, 'index.html', {'q': soz, 'natija': natija})
#   return render(request, 'hello.html', {'ism': 'Omonboy' })

def salom2(request):
	return HttpResponse('MENING SAHIFAM !!!')








#  def my_view(request):
#   context = {
#     'ad_size': '300x250',
#     'ad_targeting': 'sports'
#   }

#def hello3(request):
#	return HttpResponse('MENING SAHIFAMga hush kelibsiz !!!')
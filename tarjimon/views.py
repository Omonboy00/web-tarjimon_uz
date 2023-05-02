from django.shortcuts import render
from django.http import  HttpResponse
from .models import Lugat


# from datetime import datetime

# from django.contrib.auth import authenticate, login
# from django.shortcuts import render, redirect



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

def ads(request):
	 return render(request, 'ads.html')



# class VisitMiddleware:
#     def __init__(self, get_response):
#         self.get_response = get_response

#     def __call__(self, request):
#         if not request.user.is_authenticated:
#             # assuming you have a Visit model to store visit data
#             visit = Visit.objects.create(
#                 ip_address=request.META.get('REMOTE_ADDR'),
#                 timestamp=datetime.now(),
#             )
#             visit.save()

#         response = self.get_response(request)
#         return response





# def login_view(request):
#     if request.method == 'POST':
#         username = request.POST.get('username')
#         password = request.POST.get('password')
#         user = authenticate(request, username=username, password=password)
#         if user is not None:
#             login(request, user)
#             return redirect('home') # change this to your actual homepage
#         else:
#             error = 'Invalid username or password'
#     else:
#         error = ''

#     return render(request, 'login.html', {'error': error})



#  def my_view(request):
#   context = {
#     'ad_size': '300x250',
#     'ad_targeting': 'sports'
#   }

#def hello3(request):
#	return HttpResponse('MENING SAHIFAMga hush kelibsiz !!!')